<?php

namespace spawnApp\Database\Migrations;

use spawn\system\Core\Base\Helper\DatabaseHelper;
use spawn\system\core\base\Migration;
use spawnApp\Database\ModuleActionTable;

class M1621233972ModuleActionTableSetup extends Migration {
    
    public static function getUnixTimestamp(): int
    {
        //Do not edit this!
        return 1621233972;
    }

    function run(DatabaseHelper $dbHelper)
    {
        $moduleActionTable = new ModuleActionTable();
        $moduleActionTable->create($dbHelper);
    }


}
