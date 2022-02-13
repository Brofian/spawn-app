<?php

namespace SpawnCore\Defaults\Commands;


use bin\spawn\IO;
use Doctrine\DBAL\Exception;
use SpawnCore\System\Custom\FoundationStorage\AbstractCommand;
use SpawnCore\System\Custom\Throwables\DatabaseConnectionException;
use SpawnCore\System\Database\Entity\TableDefinition\InvalidForeignKeyConstraintException;
use SpawnCore\System\Database\Helpers\DatabaseStructureHelper;

class DatabaseSetupMinimalCommand extends AbstractCommand {

    public static function getCommand(): string
    {
        return 'database:setup-minimal';
    }

    public static function getShortDescription(): string
    {
        return 'Sets up the database with the minimal requirements';
    }

    public static function getParameters(): array
    {
        return [];
    }

    /**
     * @throws Exception
     * @throws DatabaseConnectionException
     * @throws InvalidForeignKeyConstraintException
     */
    public function execute(array $parameters): int
    {
        IO::printWarning("> Creating basic database...");


        DatabaseStructureHelper::createBasicDatabaseStructure();

        IO::printSuccess('> Created basic database structure!');
        return 0;
    }
}