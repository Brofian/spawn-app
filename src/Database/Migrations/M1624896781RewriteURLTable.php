<?php declare(strict_types=1);

namespace spawnApp\Database\Migrations;

use spawn\system\Core\Base\Helper\DatabaseHelper;
use spawn\system\Core\base\Migration;
use spawnApp\Database\RewriteURLTable;

class M1624896781RewriteURLTable extends Migration {

    public static function getUnixTimestamp(): int
    {
        //Do not edit this!
        return 1624896781;
    }

    function run(DatabaseHelper $dbHelper)
    {
        $rewriteTable = new RewriteURLTable();

        if($dbHelper->doesTableExist($rewriteTable->getTableName())) return;

        $rewriteTable->create($dbHelper);

    }

}
