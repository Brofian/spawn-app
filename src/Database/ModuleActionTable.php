<?php

namespace spawnApp\Database;

use bin\spawn\IO;
use spawn\Database\StructureTables\SpawnModules;
use spawn\system\Core\Base\Database\DatabaseColumn;
use spawn\system\Core\Base\Database\DatabaseTable;
use spawn\system\Core\Base\Database\Storage\DatabaseIndex;
use spawn\system\Core\Base\Database\Storage\DatabaseType;
use spawn\system\Core\Base\Helper\DatabaseHelper;

class ModuleActionTable extends DatabaseTable {

    public function __construct(bool $hasId = true, bool $hasCreatedAt = true, bool $hasUpdatedAt = true)
    {
        parent::__construct($hasId, $hasCreatedAt, $hasUpdatedAt);
    }

    /**
     * @inheritDoc
     */
    public function init(): bool
    {
        $col = new DatabaseColumn("class", DatabaseType::VARCHAR);
        $col->setLength(DatabaseColumn::VARCHAR_MAX);
        $col->setCanBeNull(false);
        $this->addColumn($col);

        $col = new DatabaseColumn("action", DatabaseType::VARCHAR);
        $col->setLength(DatabaseColumn::VARCHAR_MAX);
        $col->setCanBeNull(false);
        $this->addColumn($col);

        $col = new DatabaseColumn("identifier", DatabaseType::VARCHAR);
        $col->setLength(DatabaseColumn::VARCHAR_SMALL);
        $col->setIndex(DatabaseIndex::UNIQUE);
        $col->setCanBeNull(false);
        $this->addColumn($col);

        $col = new DatabaseColumn("custom_url", DatabaseType::VARCHAR);
        $col->setLength(DatabaseColumn::VARCHAR_MAX);
        $this->addColumn($col);

        $col = new DatabaseColumn("module_id", DatabaseType::INT);
        $col->setCanBeNull(false);
        $this->addColumn($col);
        $this->setOnDeleteCascade("module_id", SpawnModules::TABLENAME, SpawnModules::RAW_COL_ID);

        return true;
    }

    /**
     * @inheritDoc
     */
    public function getTableName(): string
    {
        return "spawn_module_actions";
    }

    /**
     * @inheritDoc
     */
    public function afterCreation(DatabaseHelper $dbhelper)
    {
    }
}