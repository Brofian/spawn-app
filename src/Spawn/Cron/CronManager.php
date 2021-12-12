<?php

namespace spawnCore\Cron;


use spawnApp\Database\CronTable\CronEntity;
use spawnApp\Database\CronTable\CronRepository;
use spawnCore\ServiceSystem\Service;
use spawnCore\ServiceSystem\ServiceContainerProvider;
use spawnCore\ServiceSystem\ServiceTags;

class CronManager
{

    protected CronRepository $cronRepository;

    public function __construct(
        CronRepository $cronRepository
    )
    {
        $this->cronRepository = $cronRepository;
    }


    public function executeCrons(): void {

        $cronServices = $this->gatherCrons();
        foreach($cronServices as $cronService) {

            //create cron entry
            $cronEntity = $this->createCronEntry($cronService->getClass());

            /** @var AbstractCron $instance */
            $instance = $cronService->getInstance();

            //execute cronjob
            try {
                $returnCode = $instance->run();
                $result = $instance->getOutput();
            }
            catch (\Exception $e) {
                $returnCode = $e->getCode();
                $result = $instance->getOutput() . PHP_EOL . PHP_EOL . $e->getMessage();
            }

            //update cron entry to error or success
            $this->updateCronEntry($cronEntity, $returnCode, $result);
        }

    }

    protected function createCronEntry(string $action): CronEntity {
        $entity = new CronEntity($action, '', CronStates::RUNNING);
        $this->cronRepository->upsert($entity);
        return $entity;
    }

    protected function updateCronEntry(CronEntity $entity, int $returnCode, string $result): void {
        $state = $returnCode ? CronStates::ERROR : CronStates::SUCCESS;
        $entity->setState($state);
        $entity->setResult($result);

        $this->cronRepository->upsert($entity);
    }


    /**
     * @return Service[]
     */
    protected function gatherCrons(): array {
        $cronServicesToRun = [];

        /** @var Service[] $cronServices */
        $cronServices = ServiceContainerProvider::getServiceContainer()->getServicesByTag(ServiceTags::CRON_SERVICE);
        $cronTimeManager = new CronTimeManager();

        //only return the crons with a valid and matching pattern
        foreach($cronServices as $cronService) {
            /** @var AbstractCron $serviceClass */
            $serviceClass = $cronService->getClass();
            if($cronTimeManager->shouldCronBeExecuted($serviceClass::getCronTime())) {
                $cronServicesToRun[] = $cronService;
            }
        }

        return $cronServicesToRun;
    }



}
