<?php

namespace spawnApp\Services\Commands;

use bin\spawn\IO;
use spawn\system\Core\Custom\AbstractCommand;
use spawn\system\Core\Helper\FrameworkHelper\DatabaseStructureHelper;

class DatabaseSetupMinimalCommand extends AbstractCommand {

    public static function getCommand(): string
    {
        return 'database:setup-minimal';
    }

    public static function getShortDescription(): string
    {
        return 'Sets up the database with the minimal requirements';
    }

    protected static function getParameters(): array
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