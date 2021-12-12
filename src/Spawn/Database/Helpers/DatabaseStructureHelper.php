<?php declare(strict_types=1);

namespace spawnCore\Database\Helpers;


use spawnApp\Database\MigrationTable\MigrationTable;
use spawnApp\Database\ModuleTable\ModuleTable;
use spawnCore\Database\Entity\TableDefinition\AbstractTable;
use spawnCore\ServiceSystem\ServiceContainerProvider;
use spawnCore\ServiceSystem\ServiceTags;

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