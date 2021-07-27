<?php declare(strict_types=1);

namespace spawnApp\Database;

use spawn\system\Core\Base\Database\DatabaseColumn;
use spawn\system\Core\Base\Database\DatabaseTable;
use spawn\system\Core\Base\Database\Query\QueryBuilder;
use spawn\system\Core\Base\Database\Storage\DatabaseType;
use spawn\system\Core\Base\Helper\DatabaseHelper;
use spawn\system\Core\Helper\UUID;

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
        $qb = new QueryBuilder($dbhelper->getConnection());
        $qb->insert()->into($this->getTableName())
            ->setValue('id', UUID::randomBytes())
            ->setValue('c_url', '/')
            ->setValue('replacement_url', '/?controller=system.fallback.404&action=error404')
            ->execute();
    }
}