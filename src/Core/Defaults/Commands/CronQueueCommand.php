<?php

namespace SpawnCore\Defaults\Commands;

use Exception;
use SpawnCore\System\Cron\CronManager;
use SpawnCore\System\Custom\FoundationStorage\AbstractCommand;
use SpawnCore\System\ServiceSystem\ServiceContainerProvider;

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