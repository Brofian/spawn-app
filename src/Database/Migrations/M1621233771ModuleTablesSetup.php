<?php

namespace spawnApp\Database\Migrations;

use spawn\system\Core\Base\Helper\DatabaseHelper;
use spawn\system\core\base\Migration;
use spawnApp\Database\ModuleActionTable;
use spawnApp\Database\ModuleTable;

class M1621233771ModuleTablesSetup extends Migration {
    
    public static function getUnixTimestamp(): int
    {
        //Do not edit this!
        return 1621233771;
    }

    function run(DatabaseHelper $dbHelper)
    {
        $moduleTable = new ModuleTable();
        $moduleTable->create($dbHelper);
    }

}
