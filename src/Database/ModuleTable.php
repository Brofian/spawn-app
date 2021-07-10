<?php

namespace spawnApp\Database;

use spawn\system\Core\Base\Database\DatabaseColumn;
use spawn\system\Core\Base\Database\DatabaseTable;
use spawn\system\Core\Base\Database\Storage\DatabaseType;
use spawn\system\Core\Base\Helper\DatabaseHelper;

class ModuleTable extends DatabaseTable {

    public function __construct(bool $hasId = true, bool $hasCreatedAt = true, bool $hasUpdatedAt = true)
    {
        parent::__construct($hasId, $hasCreatedAt, $hasUpdatedAt);
    }

    /**
     * @inheritDoc
     */
    public function init(): bool
    {
        $col = new DatabaseColumn("slug", DatabaseType::VARCHAR);
        $col->setLength(DatabaseColumn::VARCHAR_MAX);
        $col->setCanBeNull(false);
        $this->addColumn($col);

        $col = new DatabaseColumn("path", DatabaseType::VARCHAR);
        $col->setLength(DatabaseColumn::VARCHAR_MAX);
        $col->setCanBeNull(false);
        $this->addColumn($col);

        $col = new DatabaseColumn("active", DatabaseType::BOOLEAN);
        $col->setDefault(false);
        $this->addColumn($col);

        $col = new DatabaseColumn("informations", DatabaseType::VARCHAR);
        $col->setLength(DatabaseColumn::VARCHAR_MAX);
        $col->setCanBeNull(false);
        $this->addColumn($col);

        $col = new DatabaseColumn("ressource_config", DatabaseType::VARCHAR);
        $col->setLength(DatabaseColumn::VARCHAR_MAX);
        $col->setCanBeNull(true);
        $this->addColumn($col);


        return true;
    }

    /**
     * @inheritDoc
     */
    public function getTableName(): string
    {
        return "spawn_modules";
    }

    /**
     * @inheritDoc
     */
    public function afterCreation(DatabaseHelper $dbhelper)
    {
        // TODO: Implement afterCreation() method.
    }
}