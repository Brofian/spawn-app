<?php

namespace spawnApp\Services\Commands;


use bin\spawn\IO;
use spawnCore\Custom\FoundationStorage\AbstractCommand;
use spawnCore\Database\Helpers\DatabaseStructureHelper;

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
     * @inheritDoc
     */
    public function execute(array $parameters): int
    {
        IO::printWarning("> Creating basic database...");

        $dbStructureHelper = new DatabaseStructureHelper();
        $dbStructureHelper->createBasicDatabaseStructure();

        IO::printSuccess('> Created basic database structure!');
        return 0;
    }
}