<?php

namespace spawnApp\Database\Migrations;

use spawn\system\Core\Base\Helper\DatabaseHelper;
use spawn\system\core\base\Migration;
use spawn\system\core\database\SpawnMigrationTable;

class MigrationTableSetup extends Migration {


    public static function getUnixTimestamp(): int
    {
        //number of seconds since 01.01.1990
        return 0;
    }

    function run(DatabaseHelper $dbHelper)
    {
        $spawnMigrationTable = new SpawnMigrationTable();
        $spawnMigrationTable->create($dbHelper);
    }
}