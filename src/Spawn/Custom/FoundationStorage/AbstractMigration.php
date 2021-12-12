<?php declare(strict_types=1);

namespace spawnCore\Custom\FoundationStorage;

use spawnCore\Database\Helpers\DatabaseHelper;

abstract class AbstractMigration
{

    /**
     * Migration constructor.
     */
    public function __construct()
    {
    }

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