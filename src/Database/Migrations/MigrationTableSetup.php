<?php

namespace webuApp\Database\Migrations;

use webu\system\Core\Base\Helper\DatabaseHelper;
use webu\system\core\base\Migration;
use webu\system\core\database\WebuMigrationTable;

class MigrationTableSetup extends Migration {


    public static function getUnixTimestamp(): int
    {
        //number of seconds since 01.01.1990
        return 0;
    }

    function run(DatabaseHelper $dbHelper)
    {
        $webuMigrationTable = new WebuMigrationTable();
        $webuMigrationTable->create($dbHelper);
    }
}