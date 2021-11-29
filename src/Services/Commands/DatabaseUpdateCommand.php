<?php

namespace spawnApp\Services\Commands;

use bin\spawn\IO;
use spawn\system\Core\Custom\AbstractCommand;
use spawn\system\Core\Helper\FrameworkHelper\DatabaseStructureHelper;

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
     * @inheritDoc
     */
    public function execute(array $parameters): int
    {
        IO::printWarning("> Updating database...");

        $dbStructureHelper = new DatabaseStructureHelper();
        $dbStructureHelper->createDatabaseStructure();

        IO::printSuccess('> Updated database successfully!');
        return 0;
    }
}