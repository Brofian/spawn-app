<?php

namespace spawnApp\Database\Migrations;

use spawnApp\Database\ConfigurationTable\ConfigurationEntity;
use spawnApp\Database\ConfigurationTable\ConfigurationRepository;
use spawnApp\Database\SeoUrlTable\SeoUrlEntity;
use spawnApp\Database\SeoUrlTable\SeoUrlRepository;
use spawnCore\Cron\CronStates;
use spawnCore\Custom\FoundationStorage\AbstractMigration;
use spawnCore\Database\Criteria\Criteria;
use spawnCore\Database\Criteria\Filters\EqualsFilter;
use spawnCore\Database\Helpers\DatabaseHelper;

class Migration1641729911SetDefaultConfigValues extends AbstractMigration {

    protected ConfigurationRepository $configurationRepository;
    protected SeoUrlRepository $seoUrlRepository;

    public function __construct(
        ConfigurationRepository $configurationRepository,
        SeoUrlRepository $seoUrlRepository
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

    function run(DatabaseHelper $dbHelper)
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
        
        