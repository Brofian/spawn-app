<?php declare(strict_types=1);

namespace spawnApp\Database;

use spawn\system\Core\Base\Database\DatabaseColumn;
use spawn\system\Core\Base\Database\DatabaseTable;
use spawn\system\Core\Base\Database\Storage\DatabaseType;
use spawn\system\Core\Base\Helper\DatabaseHelper;

class RewriteURLTable extends DatabaseTable {

    public function __construct(bool $hasId = true, bool $hasCreatedAt = true, bool $hasUpdatedAt = true)
    {
        parent::__construct($hasId, $hasCreatedAt, $hasUpdatedAt);
    }

    public function init(): bool
    {
        $col = new DatabaseColumn("c_url", DatabaseType::VARCHAR);
        $col->setLength(DatabaseColumn::VARCHAR_MAX);
        $col->setCanBeNull(false);
        $this->addColumn($col);

        $col = new DatabaseColumn("replacement_url", DatabaseType::VARCHAR);
        $col->setLength(DatabaseColumn::VARCHAR_MAX);
        $col->setCanBeNull(false);
        $this->addColumn($col);

        return true;
    }


    public function getTableName(): string
    {
        return "spawn_rewrite_urls";
    }


    public function afterCreation(DatabaseHelper $dbhelper)
    {
        $dbhelper->query('INSERT INTO '.$this->getTableName().'
            (c_url, replacement_url)
            VALUES ("/", "/?controller=system.fallback.404&action=error404")
        ');
    }
}