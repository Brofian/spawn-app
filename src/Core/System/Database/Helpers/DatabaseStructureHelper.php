<?php declare(strict_types=1);

namespace SpawnCore\System\Database\Helpers;


use Doctrine\DBAL\Exception;
use SpawnCore\Defaults\Database\MigrationTable\MigrationTable;
use SpawnCore\Defaults\Database\ModuleTable\ModuleTable;
use SpawnCore\System\Custom\Throwables\DatabaseConnectionException;
use SpawnCore\System\Database\Entity\RepositoryException;
use SpawnCore\System\Database\Entity\TableDefinition\AbstractTable;
use SpawnCore\System\Database\Entity\TableDefinition\InvalidForeignKeyConstraintException;
use SpawnCore\System\ServiceSystem\ServiceContainerProvider;
use SpawnCore\System\ServiceSystem\ServiceTags;

class DatabaseStructureHelper
{

    /**
     * @throws Exception
     * @throws DatabaseConnectionException
     * @throws InvalidForeignKeyConstraintException
     * @throws RepositoryException
     */
    public static function createDatabaseStructure(): void
    {
        $serviceContainer = ServiceContainerProvider::getServiceContainer();

        $dbTableServices = $serviceContainer->getServicesByTag(ServiceTags::DATABASE_TABLE);

        foreach ($dbTableServices as $tableService) {
            /** @var AbstractTable $table */
            $table = $tableService->getInstance();

            $table->upsertTable();
        }

    }

    /**
     * @throws DatabaseConnectionException
     * @throws Exception
     * @throws InvalidForeignKeyConstraintException
     */
    public static function createBasicDatabaseStructure(): void
    {

        //create migration table
        (new MigrationTable())->upsertTable();

        //create module table
        (new ModuleTable())->upsertTable();

    }


}