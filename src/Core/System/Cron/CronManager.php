<?php

namespace SpawnCore\System\Cron;


use Exception;
use SpawnCore\Defaults\Database\CronTable\CronEntity;
use SpawnCore\Defaults\Database\CronTable\CronRepository;
use SpawnCore\System\Custom\Throwables\DatabaseConnectionException;
use SpawnCore\System\Custom\Throwables\WrongEntityForRepositoryException;
use SpawnCore\System\ServiceSystem\Service;
use SpawnCore\System\ServiceSystem\ServiceContainerProvider;
use SpawnCore\System\ServiceSystem\ServiceTags;

class CronManager
{

    protected CronRepository $cronRepository;

    public function __construct(
        CronRepository $cronRepository
    )
    {
        $this->cronRepository = $cronRepository;
    }


    /**
     * @throws DatabaseConnectionException
     * @throws WrongEntityForRepositoryException
     * @throws \Doctrine\DBAL\Exception
     */
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
            catch (Exception $e) {
                $returnCode = $e->getCode();
                $result = $instance->getOutput() . PHP_EOL . PHP_EOL . $e->getMessage();
            }

            //update cron entry to error or success
            $this->updateCronEntry($cronEntity, $returnCode, $result);
        }

    }

    /**
     * @throws \Doctrine\DBAL\Exception
     * @throws DatabaseConnectionException
     * @throws WrongEntityForRepositoryException
     */
    protected function createCronEntry(string $action): CronEntity {
        $entity = new CronEntity($action, '', CronStates::RUNNING);
        $this->cronRepository->upsert($entity);
        return $entity;
    }

    /**
     * @throws DatabaseConnectionException
     * @throws WrongEntityForRepositoryException
     * @throws \Doctrine\DBAL\Exception
     */
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
