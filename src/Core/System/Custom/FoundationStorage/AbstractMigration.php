<?php declare(strict_types=1);

namespace SpawnCore\System\Custom\FoundationStorage;

use SpawnCore\System\Database\Helpers\DatabaseHelper;

abstract class AbstractMigration
{

    /**
     * @return int
     */
    abstract public static function getUnixTimestamp(): int;

    /**
     * @param DatabaseHelper $dbHelper
     * @return mixed
     */
    abstract function run(DatabaseHelper $dbHelper);


}