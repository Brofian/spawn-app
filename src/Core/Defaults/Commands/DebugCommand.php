<?php

namespace SpawnCore\Defaults\Commands;


use SpawnCore\System\Custom\FoundationStorage\AbstractCommand;
use SpawnCore\System\Database\Entity\TableRepository;

class DebugCommand extends AbstractCommand  {

    protected TableRepository $moduleRepository;

    public function __construct(
        TableRepository $moduleRepository
    )
    {
        $this->moduleRepository = $moduleRepository;
    }

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

        return 0;
    }

}