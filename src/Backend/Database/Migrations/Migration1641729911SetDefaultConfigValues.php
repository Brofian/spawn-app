<?php declare(strict_types = 1);
namespace SpawnBackend\Database\Migrations;

use Doctrine\DBAL\Exception;
use SpawnCore\Defaults\Database\ConfigurationTable\ConfigurationEntity;
use SpawnCore\Defaults\Database\SeoUrlTable\SeoUrlEntity;
use SpawnCore\System\Custom\FoundationStorage\AbstractMigration;
use SpawnCore\System\Custom\Throwables\DatabaseConnectionException;
use SpawnCore\System\Custom\Throwables\WrongEntityForRepositoryException;
use SpawnCore\System\Database\Criteria\Criteria;
use SpawnCore\System\Database\Criteria\Filters\EqualsFilter;
use SpawnCore\System\Database\Entity\RepositoryException;
use SpawnCore\System\Database\Entity\TableRepository;
use SpawnCore\System\Database\Helpers\DatabaseHelper;

class Migration1641729911SetDefaultConfigValues extends AbstractMigration {

    protected TableRepository $configurationRepository;
    protected TableRepository $seoUrlRepository;

    public function __construct(
        TableRepository $configurationRepository,
        TableRepository $seoUrlRepository
    )
    {
        $this->configurationRepository = $configurationRepository;
        $this->seoUrlRepository = $seoUrlRepository;
    }


    public static function getUnixTimestamp(): int
    {
        //Do not edit this!
        return 1641729911;
    }

    /**
     * @throws DatabaseConnectionException
     * @throws Exception
     * @throws RepositoryException
     * @throws WrongEntityForRepositoryException
     */
    public function run(DatabaseHelper $dbHelper)
    {
        //set fallback
        $fallbackAction = $this->seoUrlRepository->search(new Criteria(
            new EqualsFilter('controller', 'system.fallback.404'),
            new EqualsFilter('action', 'error404Action')
        ))->first();
        if($fallbackAction instanceof SeoUrlEntity) {
            $this->setConfig('config_system_fallback_method', $fallbackAction->getId());
        }

    }

    /**
     * @throws DatabaseConnectionException
     * @throws RepositoryException
     * @throws Exception
     * @throws WrongEntityForRepositoryException
     */
    protected function setConfig(string $internalName, string $value): void {
        $config = $this->configurationRepository->search(new Criteria(
            new EqualsFilter('internalName', $internalName)
        ))->first();
        if($config instanceof ConfigurationEntity) {
            $config->setValue($value);
            $this->configurationRepository->upsert($config);
        }
    }

}        
        
        