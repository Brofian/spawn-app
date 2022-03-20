<?php

namespace SpawnCore\Defaults\Commands;


use SpawnCore\Defaults\Database\ModuleTable\ModuleRepository;
use SpawnCore\System\Custom\FoundationStorage\AbstractCommand;

class DebugCommand extends AbstractCommand  {

    protected ModuleRepository $moduleRepository;

    public function __construct(
        ModuleRepository $moduleRepository
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