<?php declare(strict_types=1);

namespace SpawnCore\System\Database\Helpers;


use SpawnCore\Defaults\Database\MigrationTable\MigrationTable;
use SpawnCore\Defaults\Database\ModuleTable\ModuleTable;
use SpawnCore\System\Database\Entity\TableDefinition\AbstractTable;
use SpawnCore\System\ServiceSystem\ServiceContainerProvider;
use SpawnCore\System\ServiceSystem\ServiceTags;

class DatabaseStructureHelper
{

    public static function createDatabaseStructure()
    {
        $serviceContainer = ServiceContainerProvider::getServiceContainer();

        $dbTableServices = $serviceContainer->getServicesByTag(ServiceTags::DATABASE_TABLE);

        foreach ($dbTableServices as $tableService) {
            /** @var AbstractTable $table */
            $table = $tableService->getInstance();

            $table->upsertTable();
        }

    }

    public static function createBasicDatabaseStructure()
    {

        //create migration table
        (new MigrationTable())->upsertTable();

        //create module table
        (new ModuleTable())->upsertTable();

    }


}