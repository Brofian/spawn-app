<?php

namespace SpawnCore\Defaults\Services\Commands;


use bin\spawn\IO;
use SpawnCore\Defaults\Database\ModuleTable\ModuleRepository;
use SpawnCore\System\Custom\FoundationStorage\AbstractCommand;
use SpawnCore\System\Database\Criteria\Criteria;
use SpawnCore\System\Database\Criteria\Filters\AndFilter;
use SpawnCore\System\Database\Criteria\Filters\BetweenFilter;
use SpawnCore\System\Database\Criteria\Filters\EqualsFilter;
use SpawnCore\System\Database\Criteria\Filters\LikeFilter;
use SpawnCore\System\Database\Criteria\Filters\OrFilter;

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