<?php

namespace spawnCore\Cron\Jobs;

use spawn\system\Core\Base\Database\DatabaseConnection;
use spawnApp\Database\CronTable\CronRepository;
use spawnApp\Database\CronTable\CronTable;
use spawnCore\Cron\AbstractCron;
use spawnCore\Cron\CronStates;

class CleanCronQueue extends AbstractCron {

    protected CronRepository $cronRepository;

    public function __construct(
        CronRepository $cronRepository
    )
    {
        $this->cronRepository = $cronRepository;
    }


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
        //get Timestamp from one week ago
        $lastWeek = new \DateTime('-1 week');

        $cronEntities = $this->cronRepository->search([
            'state' => CronStates::SUCCESS,
            'updatedAt' => [
                'operator' => '>',
                'value' => $lastWeek->format('Y-m-d')
            ]
        ]);

        dd('TODO: Find out, why this does not return any lines (the cause is the updatedAt condition)');


        //TODO cleanup

        $this->addInfo('');

        return 0;
    }
}