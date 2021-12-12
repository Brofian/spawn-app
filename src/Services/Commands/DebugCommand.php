<?php

namespace spawnApp\Services\Commands;


use bin\spawn\IO;
use spawnCore\Custom\FoundationStorage\AbstractCommand;

class DebugCommand extends AbstractCommand  {

    public static function getCommand(): string {
        return 'debug:output';
    }

    public static function getShortDescription(): string    {
        return 'Gives some Debug output';
    }

    public static function getParameters(): array   {
        return [
            'debug' => ['debug', 'd'],
            'test' => 't',
        ];
    }

    public function execute(array $parameters): int  {
        IO::printLine("YAY");
        dump($parameters);
        IO::printLine("YAY");

        return 0;
    }

}