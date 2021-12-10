<?php

namespace spawnCore\Cron\Jobs;

use spawnCore\Cron\AbstractCron;

class CleanCronQueue extends AbstractCron {

    public static function getCronTime(): string
    {
        //      __________ minute (0-60)
        //      | ________ hour (0-23)
        //      | | ______ day of month (1-31)
        //      | | | ____ month (1-12)
        //      | | | | __ day of week (0-6)
        //      | | | | |
        //return '3 10 * * 0';
        return '* * * * *';
    }

    public function run(): int
    {

        $this->addInfo('Hello world');

        return 0;
    }
}