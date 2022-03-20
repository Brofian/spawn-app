<?php declare(strict_types = 1);
namespace SpawnCore\System\Cron\Jobs;

use DateTime;
use SpawnCore\Defaults\Database\CronTable\CronEntity;
use SpawnCore\Defaults\Database\CronTable\CronRepository;
use SpawnCore\System\Cron\AbstractCron;
use SpawnCore\System\Cron\CronStates;
use SpawnCore\System\Custom\Gadgets\UUID;
use SpawnCore\System\Custom\Throwables\DatabaseConnectionException;
use SpawnCore\System\Database\Criteria\Criteria;
use SpawnCore\System\Database\Criteria\Filters\AndFilter;
use SpawnCore\System\Database\Criteria\Filters\EqualsFilter;
use SpawnCore\System\Database\Criteria\Filters\InFilter;
use SpawnCore\System\Database\Criteria\Filters\InvalidFilterValueException;
use SpawnCore\System\Database\Criteria\Filters\LessThanFilter;
use SpawnCore\System\Database\Entity\InvalidRepositoryInteractionException;
use SpawnCore\System\Database\Entity\RepositoryException;

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
        return   '* * * * *';
    }

    /**
     * @throws DatabaseConnectionException
     * @throws InvalidFilterValueException
     * @throws InvalidRepositoryInteractionException
     * @throws RepositoryException
     */
    public function run(): int
    {
        //get Timestamp from one week ago
        $lastWeek = new DateTime('-1 day');

        $cronEntities = $this->cronRepository->search(
            new Criteria(
                new AndFilter(
                    new EqualsFilter('state', CronStates::SUCCESS),
                    new LessThanFilter('updatedAt', $lastWeek->format('Y-m-d H:i:s'))
                )
            )
        );

        $this->addInfo('Start cleaning ' . $cronEntities->count() . ' old entities!');

        $ids = [];
        /** @var CronEntity $cronEntity */
        foreach ($cronEntities as $cronEntity) {
            $ids[] = UUID::hexToBytes($cronEntity->getId());
        }

        $this->cronRepository->delete(
            new Criteria(
                new InFilter('id', $ids)
            )
        );

        $this->addInfo('Finished cleaning entities');

        return 0;
    }
}