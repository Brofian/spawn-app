<?php

namespace SpawnCore\Defaults\Commands;


use SpawnCore\Defaults\Database\ModuleTable\ModuleRepository;
use SpawnCore\System\Custom\FoundationStorage\AbstractCommand;
use SpawnCore\System\Database\Helpers\DatabaseHelper;
use SpawnCore\System\ServiceSystem\ServiceContainerProvider;

class SpawnBuildCommand extends AbstractCommand  {

    protected DatabaseHelper $databaseHelper;
    protected ModuleRepository $moduleRepository;

    public function __construct(
        DatabaseHelper $databaseHelper,
        ModuleRepository $moduleRepository
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