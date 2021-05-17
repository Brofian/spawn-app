<?php

namespace webuApp\Database\Migrations;

use webu\system\Core\Base\Helper\DatabaseHelper;
use webu\system\core\base\Migration;
use webuApp\Database\ModuleActionTable;
use webuApp\Database\ModuleTable;

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
