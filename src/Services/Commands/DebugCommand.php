<?php

namespace spawnApp\Services\Commands;


use bin\spawn\IO;
use spawnApp\Database\ModuleTable\ModuleRepository;
use spawnCore\Custom\FoundationStorage\AbstractCommand;
use spawnCore\Database\Criteria\Criteria;
use spawnCore\Database\Criteria\Filters\AndFilter;
use spawnCore\Database\Criteria\Filters\BetweenFilter;
use spawnCore\Database\Criteria\Filters\EqualsFilter;
use spawnCore\Database\Criteria\Filters\LikeFilter;
use spawnCore\Database\Criteria\Filters\OrFilter;

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