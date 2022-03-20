<?php

namespace SpawnCore\Defaults\Commands;


use bin\spawn\IO;
use Doctrine\DBAL\Exception;
use SpawnCore\System\Custom\FoundationStorage\AbstractCommand;
use SpawnCore\System\Custom\Throwables\DatabaseConnectionException;
use SpawnCore\System\Custom\Throwables\SubscribeToNotAnEventException;
use SpawnCore\System\Database\Entity\RepositoryException;
use SpawnCore\System\Database\Entity\TableDefinition\InvalidForeignKeyConstraintException;
use SpawnCore\System\Database\Helpers\DatabaseStructureHelper;

class DatabaseUpdateCommand extends AbstractCommand {

    public static function getCommand(): string
    {
        return 'database:update';
    }

    public static function getShortDescription(): string
    {
        return 'Updates the database by the definitions inside of modules';
    }

    public static function getParameters(): array
    {
        return [];
    }

    /**
     * @param array $parameters
     * @return int
     * @throws Exception
     * @throws DatabaseConnectionException
     * @throws InvalidForeignKeyConstraintException
     * @throws RepositoryException
     * @throws SubscribeToNotAnEventException
     */
    public function execute(array $parameters): int
    {
        IO::printWarning("> Updating database...");

        DatabaseStructureHelper::createDatabaseStructure();

        IO::printSuccess('> Updated database successfully!');
        return 0;
    }
}