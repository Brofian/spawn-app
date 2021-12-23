<?php

namespace spawnApp\Database\ModuleTable;

use spawnCore\Database\Entity\TableDefinition\DefaultColumns\BooleanColumn;
use spawnCore\Database\Entity\TableDefinition\DefaultColumns\CreatedAtColumn;
use spawnCore\Database\Entity\TableDefinition\DefaultColumns\JsonColumn;
use spawnCore\Database\Entity\TableDefinition\DefaultColumns\StringColumn;
use spawnCore\Database\Entity\TableDefinition\DefaultColumns\UpdatedAtColumn;
use spawnCore\Database\Entity\TableDefinition\DefaultColumns\UuidColumn;
use spawnCore\Database\Entity\TableDefinition\AbstractTable;

class ModuleTable extends AbstractTable {

    public const TABLE_NAME = 'spawn_modules';

    public function getTableColumns(): array
    {
        return [
            new UuidColumn('id', null),
            new StringColumn('slug', false),
            new StringColumn('path', false),
            new StringColumn('namespace', false),
            new BooleanColumn('active', false),
            new JsonColumn('information'),
            new JsonColumn('resourceConfig'),
            new CreatedAtColumn(),
            new UpdatedAtColumn()
        ];
    }

    public function getTableName(): string
    {
        return self::TABLE_NAME;
    }
}