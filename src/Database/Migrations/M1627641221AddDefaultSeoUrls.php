<?php

namespace Spawnapp\Database\Migrations;

use spawn\system\Core\Base\Database\DatabaseConnection;
use spawn\system\Core\Base\Database\Query\QueryBuilder;
use spawn\system\Core\Base\Helper\DatabaseHelper;
use spawn\system\Core\base\Migration;
use spawnApp\Database\SeoUrlTable\SeoUrlTable;

class M1627641221AddDefaultSeoUrls extends Migration {
    
    public static function getUnixTimestamp(): int
    {
        //Do not edit this!
        return 1627641221;
    }

    function run(DatabaseHelper $dbHelper)
    {
        $qb = new QueryBuilder(DatabaseConnection::getConnection());

        $currentTimestamp = time();

        $stmt = $qb->insert();
        $stmt->into(SeoUrlTable::TABLE_NAME)
            ->setValue('cUrl', '/')
            ->setValue('rewriteUrl', '/?controller=system.fallback.404&action=error404Action')
            ->setValue('createdAt', $currentTimestamp)
            ->setValue('updatedAt', $currentTimestamp)
            ->execute();

        $stmt = $qb->insert();
        $stmt->into(SeoUrlTable::TABLE_NAME)
            ->setValue('cUrl', '/backend/')
            ->setValue('rewriteUrl', '/?controller=system.backend.base&action=homeAction')
            ->setValue('createdAt', $currentTimestamp)
            ->setValue('updatedAt', $currentTimestamp)
            ->execute();

        $stmt = $qb->insert();
        $stmt->into(SeoUrlTable::TABLE_NAME)
            ->setValue('cUrl', '/backend/seo_config/overview')
            ->setValue('rewriteUrl', '/?controller=system.backend.seo_url_config&action=seoUrlOverviewAction')
            ->setValue('createdAt', $currentTimestamp)
            ->setValue('updatedAt', $currentTimestamp)
            ->execute();
    }

}
