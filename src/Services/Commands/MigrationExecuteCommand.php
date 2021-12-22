<?php

namespace spawnApp\Services\Commands;

use bin\spawn\IO;
use Doctrine\DBAL\Exception;
use spawnApp\Database\MigrationTable\MigrationEntity;
use spawnApp\Database\MigrationTable\MigrationRepository;
use spawnApp\Database\ModuleTable\ModuleEntity;
use spawnCore\Custom\FoundationStorage\AbstractCommand;
use spawnCore\Custom\FoundationStorage\AbstractMigration;
use spawnCore\Custom\Gadgets\FileEditor;
use spawnCore\Custom\Throwables\WrongEntityForRepositoryException;
use spawnCore\Database\Criteria\Criteria;
use spawnCore\Database\Entity\EntityCollection;
use spawnCore\Database\Helpers\DatabaseHelper;
use spawnCore\ServiceSystem\Service;

class MigrationExecuteCommand extends AbstractCommand {

    protected MigrationRepository $migrationRepository;
    protected DatabaseHelper $databaseHelper;

    public function __construct(
        MigrationRepository $migrationRepository,
        DatabaseHelper $databaseHelper
    )
    {
        $this->migrationRepository = $migrationRepository;
        $this->databaseHelper = $databaseHelper;
    }

    public static function getCommand(): string
    {
        return 'database:migration:execute';
    }

    public static function getShortDescription(): string
    {
        return 'Executes module migrations to update the database';
    }

    public static function getParameters(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function execute(array $parameters): int
    {
        $moduleCollection = ListModulesCommand::getModuleList();

        $migrations = $this->gatherMigrations($moduleCollection);

        $this->sortGatheredMigrations($migrations);

        $alreadyExecutedMigrations = $this->loadAlreadyExecutedMigrations();

        $migrationsToExecute = $this->filterAlreadyExecutedMigrations($migrations, $alreadyExecutedMigrations);

        $this->executeMigrations($migrationsToExecute);

        $this->saveExecutedMigrationsToDB($migrationsToExecute);


        return 0;
    }

    protected function gatherMigrations(EntityCollection $moduleCollection): array
    {
        $migrations = [];
        /** @var ModuleEntity $module */
        foreach($moduleCollection->getArray() as $module) {

            $migrationsFolder = ROOT.$module->getPath().'/src/Database/Migrations';
            if(!file_exists($migrationsFolder) || !is_dir($migrationsFolder)) {
                continue;
            }

            $migrationFiles = scandir($migrationsFolder);

            foreach($migrationFiles as $file) {
                if($file == "." || $file == "..") continue;
                $path = $migrationsFolder.'/'.$file;
                $fileContent = FileEditor::getFileContent($path);

                //read classname
                $matches = [];
                $isMigration = preg_match_all('/class ([^{]*) extends AbstractMigration/', $fileContent, $matches);
                if(!$isMigration || count($matches) < 2) continue;
                $className = $matches[1][0];

                //read namespace
                $matches = [];
                $hasNamespace = preg_match_all('/namespace ([^;]*);/', $fileContent, $matches);
                if(!$hasNamespace || count($matches) < 2) continue;
                $namespace = $matches[1][0];

                /** @var AbstractMigration $fullClassName */
                $fullClassName = $namespace . "\\" . $className;

                $migrations[] = [$fullClassName::getUnixTimestamp(),$fullClassName];
            }
        }

        return $migrations;
    }

    protected function sortGatheredMigrations(array &$migrations): void {
        //sort all migrations by their timestamp (0 -> lowest)
        usort($migrations, function($a, $b) {
            return ($a[0] < $b[0]) ? -1 : 1;
        });
    }

    protected function loadAlreadyExecutedMigrations(): array   {
        $executedMigrationEntities = $this->migrationRepository->search(new Criteria());

        $executedMigrations = [];
        /** @var Service $migrationEntity */
        foreach($executedMigrationEntities as $migrationEntity) {
            /** @var AbstractMigration $class */
            $class = $migrationEntity->getClass();
            $executedMigrations[] = $class . "-" . $class::getUnixTimestamp();
        }

        return $executedMigrations;
    }

    protected function filterAlreadyExecutedMigrations(array $migrations, array $alreadyExecutedMigrations): array {
        $migrationsToExecute = [];

        foreach($migrations as $migration) {
            $migrationTimestamp = $migration[0];
            $migrationClass = $migration[1];

            $migrationName = (string)$migrationClass;
            $migrationName .= '-'.$migrationTimestamp;

            if (!in_array($migrationName, $alreadyExecutedMigrations)) {
                $migrationsToExecute[] = $migration;
            }
        }

        return $migrationsToExecute;
    }

    /**
     * @param array $migrationsToExecute
     * @throws \Exception
     */
    protected function executeMigrations(array $migrationsToExecute): void   {

        foreach($migrationsToExecute as $migration) {
            $migrationClass = $migration[1];

            $count = 0;
            try {
                /** @var AbstractMigration $m */
                $m = new $migrationClass();
                $m->run($this->databaseHelper);

                $newMigrations[] = $migration;
                IO::printSuccess("   :: executed Migration \"$migrationClass\"", 1);
                $count++;
            } catch (\Exception $e) {
                IO::printError('Error on running migration "'.$migrationClass.'"');
                throw $e;
            }

            IO::printSuccess('> Successfully executed '.IO::BLUE_TEXT.$count.IO::GREEN_TEXT.' Migrations!');
        }

    }

    /**
     * @param array $executedMigrations
     * @throws Exception
     * @throws WrongEntityForRepositoryException
     */
    protected function saveExecutedMigrationsToDB(array $executedMigrations): void {

        foreach($executedMigrations as $newMigration) {
            $class = (string)$newMigration[1];
            $timestamp = (int)$newMigration[0];
            $migrationEntity = new MigrationEntity($class, $timestamp);

            $this->migrationRepository->upsert($migrationEntity);
        }

    }

}
