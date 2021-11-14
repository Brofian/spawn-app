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

        (new CacheClearCommand())->execute(CacheClearCommand::createParameterArray(['a'=>true]));


        return 0;
    }


}