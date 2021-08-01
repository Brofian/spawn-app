<?php

namespace spawnApp\Database\Migrations;

use spawn\system\Core\Base\Database\DatabaseConnection;
use spawn\system\Core\Base\Helper\DatabaseHelper;
use spawn\system\Core\base\Migration;
use spawn\system\Core\Helper\UUID;
use spawnApp\Database\SeoUrlTable\SeoUrlTable;

class M1627641221AddDefaultSeoUrls extends Migration {
    
    public static function getUnixTimestamp(): int
    {
        //Do not edit this!
        return 1627641221;
    }

    function run(DatabaseHelper $dbHelper)
    {
        $conn = DatabaseConnection::getConnection();

        $currentTimestamp = new \DateTime();

        $seoUrlInsertFunction = function(string $cUrl, string $rewriteUrl) use ($conn,$currentTimestamp) {
            $conn->insert(SeoUrlTable::TABLE_NAME, [
                    'id' => UUID::randomBytes(),
                    'cUrl' => $cUrl,
                    'rewriteUrl' => $rewriteUrl,
                    'createdAt' => $currentTimestamp,
                    'updatedAt' => $currentTimestamp
                ],
                [
                    \PDO::PARAM_STR,
                    \PDO::PARAM_STR,
                    \PDO::PARAM_STR,
                    'datetime',
                    'datetime'
                ]);
        };

        $seoUrlInsertFunction('/', '/?controller=system.fallback.404&action=error404Action');
        $seoUrlInsertFunction('/backend', '/?controller=system.backend.base&action=homeAction');
        $seoUrlInsertFunction('/backend/seo_config/overview', '/?controller=system.backend.seo_url_config&action=seoUrlOverviewAction');

    }

}
