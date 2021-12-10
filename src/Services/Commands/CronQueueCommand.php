<?php

namespace spawnApp\Services\Commands;

use bin\spawn\IO;
use Exception;
use spawn\system\Core\Base\Database\Definition\EntityCollection;
use spawn\system\Core\Custom\AbstractCommand;
use spawn\system\Core\Helper\FrameworkHelper\ResourceCollector;
use spawn\system\Core\Helper\ScssHelper;
use spawn\system\Core\Services\ServiceContainerProvider;
use spawnApp\Database\ModuleTable\ModuleEntity;
use spawnCore\Cron\CronManager;

class CronQueueCommand extends AbstractCommand {

    public static function getCommand(): string
    {
        return 'cron:queue:start';
    }

    public static function getShortDescription(): string
    {
        return 'Executes the cron queue';
    }

    public static function getParameters(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function execute(array $parameters): int
    {
        /** @var CronManager $cronManager */
        $cronManager = ServiceContainerProvider::getServiceContainer()->getServiceInstance('system.core.cron.manager');
        $cronManager->executeCrons();

        return 0;
    }

}