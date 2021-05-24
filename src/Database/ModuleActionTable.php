<?php

namespace webuApp\Database;

use bin\webu\IO;
use webu\Database\StructureTables\WebuModules;
use webu\system\Core\Base\Database\DatabaseColumn;
use webu\system\Core\Base\Database\DatabaseTable;
use webu\system\Core\Base\Database\Storage\DatabaseIndex;
use webu\system\Core\Base\Database\Storage\DatabaseType;
use webu\system\Core\Base\Helper\DatabaseHelper;

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
        $col->setLength(DatabaseColumn::VARCHAR_MIDDLE);
        $col->setIndex(DatabaseIndex::UNIQUE);
        $col->setCanBeNull(false);
        $this->addColumn($col);

        $col = new DatabaseColumn("custom_url", DatabaseType::VARCHAR);
        $col->setLength(DatabaseColumn::VARCHAR_MAX);
        $this->addColumn($col);

        $col = new DatabaseColumn("module_id", DatabaseType::INT);
        $col->setCanBeNull(false);
        $this->addColumn($col);
        $this->setOnDeleteCascade("module_id", WebuModules::TABLENAME, WebuModules::RAW_COL_ID);

        return true;
    }

    /**
     * @inheritDoc
     */
    public function getTableName(): string
    {
        return "webu_module_actions";
    }

    /**
     * @inheritDoc
     */
    public function afterCreation(DatabaseHelper $dbhelper)
    {
    }
}