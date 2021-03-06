<?php

namespace SpawnCore\Defaults\Commands;

use bin\spawn\IO;
use Doctrine\DBAL\Exception;
use SpawnCore\Defaults\Database\MigrationTable\MigrationEntity;
use SpawnCore\Defaults\Database\MigrationTable\MigrationRepository;
use SpawnCore\System\Custom\FoundationStorage\AbstractCommand;
use SpawnCore\System\Custom\FoundationStorage\AbstractMigration;
use SpawnCore\System\Custom\Throwables\DatabaseConnectionException;
use SpawnCore\System\Custom\Throwables\SubscribeToNotAnEventException;
use SpawnCore\System\Custom\Throwables\WrongEntityForRepositoryException;
use SpawnCore\System\Database\Criteria\Criteria;
use SpawnCore\System\Database\Entity\EntityCollection;
use SpawnCore\System\Database\Entity\RepositoryException;
use SpawnCore\System\Database\Helpers\DatabaseHelper;
use SpawnCore\System\ServiceSystem\Service;
use SpawnCore\System\ServiceSystem\ServiceContainerProvider;

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

    /**
     * @throws DatabaseConnectionException
     * @throws Exception
     * @throws RepositoryException
     * @throws SubscribeToNotAnEventException
     */
    protected function gatherMigrations(EntityCollection $moduleCollection): array
    {
        $migrationServices = ServiceContainerProvider::getServiceContainer()->getServicesByTag('base.service.migration');
        $migrations = [];
        foreach($migrationServices as $migrationService) {
            /** @var AbstractMigration $class */
            $class = $migrationService->getClass();
            $migrations[] = [$class::getUnixTimestamp(),$class, $migrationService];
        }

        return $migrations;
    }

    protected function sortGatheredMigrations(array &$migrations): void {
        //sort all migrations by their timestamp (0 -> lowest)
        usort($migrations, static function($a, $b) {
            return ($a[0] < $b[0]) ? -1 : 1;
        });
    }

    /**
     * @throws DatabaseConnectionException
     * @throws RepositoryException
     */
    protected function loadAlreadyExecutedMigrations(): array   {
        $executedMigrationEntities = $this->migrationRepository->search(new Criteria());

        $executedMigrations = [];
        /** @var MigrationEntity $migrationEntity */
        foreach($executedMigrationEntities as $migrationEntity) {
            $class = $migrationEntity->getClass();
            $executedMigrations[] = $class . "-" . $migrationEntity->getTimestamp();
        }

        return $executedMigrations;
    }

    protected function filterAlreadyExecutedMigrations(array $migrations, array $alreadyExecutedMigrations): array {
        $migrationsToExecute = [];

        foreach($migrations as $migration) {
            [$migrationTimestamp, $migrationClass] = $migration;

            $migrationName = (string)$migrationClass;
            $migrationName .= '-'.$migrationTimestamp;

            if (!in_array($migrationName, $alreadyExecutedMigrations, true)) {
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
                /** @var Service $service */
                $service = $migration[2];
                /** @var AbstractMigration $m */
                $m = $service->getInstance();
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
     * @throws DatabaseConnectionException
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
