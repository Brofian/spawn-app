<?php

namespace spawnCore\Cron;

use spawn\system\Core\Services\Service;
use spawn\system\Core\Services\ServiceContainerProvider;
use spawn\system\Core\Services\ServiceTags;

class CronManager {

    protected \DateTime $now;

    public function __construct()
    {
        $this->now = new \DateTime();
    }


    public function executeCrons(): void {

        $cronServices = $this->gatherCrons();
        foreach($cronServices as $cronService) {
            /** @var AbstractCron $instance */
            $instance = $cronService->getInstance();
            $instance->run();
        }

    }

    /**
     * @return Service[]
     */
    protected function gatherCrons(): array {
        $cronServicesToRun = [];

        /** @var Service[] $cronServices */
        $cronServices = ServiceContainerProvider::getServiceContainer()->getServicesByTag(ServiceTags::CRON_SERVICE);

        //only return the crons with a valid and matching pattern
        foreach($cronServices as $cronService) {
            /** @var AbstractCron $serviceClass */
            $serviceClass = $cronService->getClass();
            if($this->shouldCronBeExecuted($serviceClass::getCronTime())) {
                $cronServicesToRun[] = $cronService;
            }
        }

        return $cronServicesToRun;
    }

    public function shouldCronBeExecuted(string $cronPattern): bool {

        //TODO

        $cronTimings = $this->interpretCronPattern($cronPattern);





        return;
    }

    protected function interpretCronPattern(string $cronPattern): array {

        $parts = explode(' ', $cronPattern);
        if(count($parts) != 5) {
            throw new InvalidCronPatternException($cronPattern);
        }

        $minute = $this->interpretTimeDefinition($parts[0], 0, 59);
        $hour = $this->interpretTimeDefinition($parts[1], 0, 23);
        $dayOfMonth = $this->interpretTimeDefinition($parts[2], 1, 31);
        $month = $this->interpretTimeDefinition($parts[3], 1, 12);
        $dayOfWeek = $this->interpretTimeDefinition($parts[4], 1, 6);

        return [
            'minute' => $minute,
            'hour' => $hour,
            'dayOfMonth' => $dayOfMonth,
            'month' => $month,
            'dayOfWeek' => $dayOfWeek,
        ];

    }


    protected function interpretTimeDefinition(string $timeDefinition, int $min, int $max): string {
        // */x
        if(preg_match('/^\*\/\d$/g', $timeDefinition, $matches)) {
            dd($matches);
        }



        return '';
    }


}
