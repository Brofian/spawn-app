<?php

namespace spawnApp\Services\Commands;

use bin\spawn\IO;
use spawn\system\Core\Custom\AbstractCommand;

class SpawnBuildCommand extends AbstractCommand  {

    public static function getCommand(): string {
        return 'spawn:build';
    }

    public static function getShortDescription(): string    {
        return 'Builds everything';
    }

    protected static function getParameters(): array   {
        return [];
    }

    public function execute(array $parameters): int  {

        //print SPAWN
        (new PrintSpawnCommand())->execute(CacheClearCommand::createParameterArray(['a'=>true]));

        //cache caches
        (new CacheClearCommand())->execute(CacheClearCommand::createParameterArray(['a'=>true]));

        //upsert database
        //todo: include(__DIR__ . "/../database/update.php");

        //update module list
        //todo: include(__DIR__ . "/../modules/refresh-actions.php");

        //compile modules
        //todo: include(__DIR__ . "/../modules/compile-js.php");
        //todo: include(__DIR__ . "/../modules/compile-scss.php");

        return 0;
    }


}