<?php

namespace spawnApp\Database\Migrations;

use Doctrine\DBAL\Exception;
use spawnApp\Database\SeoUrlTable\SeoUrlEntity;
use spawnApp\Database\SeoUrlTable\SeoUrlRepository;
use spawnCore\Custom\FoundationStorage\AbstractMigration;
use spawnCore\Custom\Throwables\WrongEntityForRepositoryException;
use spawnCore\Database\Helpers\DatabaseHelper;
use spawnCore\ServiceSystem\ServiceContainerProvider;

class M1627641221AddDefaultSeoUrls extends AbstractMigration {
    
    public static function getUnixTimestamp(): int
    {
        //Do not edit this!
        return 1627641221;
    }

    /**
     * @param DatabaseHelper $dbHelper
     * @return mixed|void
     * @throws Exception
     * @throws WrongEntityForRepositoryException
     */
    function run(DatabaseHelper $dbHelper)
    {
        /** @var SeoUrlRepository $seoUrlRepository */
        $seoUrlRepository = ServiceContainerProvider::getServiceContainer()->getServiceInstance('system.repository.seo_urls');


        $entity = new SeoUrlEntity('/', 'system.fallback.404', 'error404Action', [], false, true);
        $seoUrlRepository->upsert($entity);

        $entity = new SeoUrlEntity('/backend', 'system.backend.base', 'homeAction', [],  true, true);
        $seoUrlRepository->upsert($entity);

        $entity = new SeoUrlEntity('/backend/seo_config/overview', 'system.backend.seo_url_config', 'seoUrlOverviewAction', [], true, true);
        $seoUrlRepository->upsert($entity);

        $entity = new SeoUrlEntity('/backend/seo_config/edit/{ctrl}/{action}', 'system.backend.seo_url_config', 'seoUrlEditAction', [], true, true);
        $seoUrlRepository->upsert($entity);

        $entity = new SeoUrlEntity('/backend/seo_config/edit/submit/{ctrl}/{action}', 'system.backend.seo_url_config', 'seoUrlEditSubmitAction',  [], true, true);
        $seoUrlRepository->upsert($entity);
    }

}
