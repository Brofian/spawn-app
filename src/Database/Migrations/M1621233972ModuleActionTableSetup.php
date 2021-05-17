<?php

namespace webuApp\Database\Migrations;

use webu\system\Core\Base\Helper\DatabaseHelper;
use webu\system\core\base\Migration;
use webuApp\Database\ModuleActionTable;

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
