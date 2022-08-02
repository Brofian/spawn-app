<?php

namespace SpawnCore\Defaults\Commands;


use Doctrine\DBAL\Exception;
use SpawnCore\System\Custom\FoundationStorage\AbstractCommand;
use SpawnCore\System\Custom\Throwables\DatabaseConnectionException;
use SpawnCore\System\Custom\Throwables\SubscribeToNotAnEventException;
use SpawnCore\System\Database\Entity\RepositoryException;
use SpawnCore\System\Database\Entity\TableDefinition\InvalidForeignKeyConstraintException;
use SpawnCore\System\Database\Entity\TableRepository;
use SpawnCore\System\Database\Helpers\DatabaseHelper;
use SpawnCore\System\ServiceSystem\ServiceContainerProvider;

class SpawnBuildCommand extends AbstractCommand  {

    protected DatabaseHelper $databaseHelper;
    protected TableRepository $moduleRepository;

    public function __construct(
        DatabaseHelper $databaseHelper,
        TableRepository $moduleRepository
    )
    {
        $this->databaseHelper = $databaseHelper;
        $this->moduleRepository = $moduleRepository;
    }


    public static function getCommand(): string {
        return 'spawn:build';
    }

    public static function getShortDescription(): string    {
        return 'Builds everything';
    }

    public static function getParameters(): array   {
        return [];
    }

    /**
     * @param array $parameters
     * @return int
     * @throws DatabaseConnectionException
     * @throws Exception
     * @throws RepositoryException
     * @throws SubscribeToNotAnEventException
     * @throws InvalidForeignKeyConstraintException
     */
    public function execute(array $parameters): int  {
        $container = ServiceContainerProvider::getServiceContainer();

        //print SPAWN
        (new PrintSpawnCommand())->execute(CacheClearCommand::createParameterArray(['a'=>true]));

        //cache caches
        (new CacheClearCommand())->execute(CacheClearCommand::createParameterArray(['a'=>true]));

        //upsert database
        (new DatabaseUpdateCommand())->execute(DatabaseUpdateCommand::createParameterArray([]));

        //update module list
        $modulesRefreshCommand = $container->getServiceInstance('system.command.module_refresh');
        $modulesRefreshCommand->execute(ModulesRefreshCommand::createParameterArray([]));

        //execute migrations
        /** @var MigrationExecuteCommand $modulesRefreshCommand */
        $migrationExecuteCommand = $container->get('system.command.migration_execute');
        $migrationExecuteCommand->execute(MigrationExecuteCommand::createParameterArray([]));

        //compile modules
        (new ThemeCompileCommand())->execute(ThemeCompileCommand::createParameterArray([]));

        return 0;
    }


}