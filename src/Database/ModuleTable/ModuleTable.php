<?php

namespace spawnApp\Database\ModuleTable;

use spawn\Core\Base\Database\Definition\TableDefinition\DefaultColumns\BooleanColumn;
use spawn\Core\Base\Database\Definition\TableDefinition\DefaultColumns\CreatedAtColumn;
use spawn\Core\Base\Database\Definition\TableDefinition\DefaultColumns\JsonColumn;
use spawn\Core\Base\Database\Definition\TableDefinition\DefaultColumns\StringColumn;
use spawn\Core\Base\Database\Definition\TableDefinition\DefaultColumns\UpdatedAtColumn;
use spawn\Core\Base\Database\Definition\TableDefinition\DefaultColumns\UuidColumn;
use spawnCore\Database\Entity\TableDefinition\AbstractTable;

class ModuleTable extends AbstractTable {

    public const TABLE_NAME = 'spawn_modules';

    public function getTableColumns(): array
    {
        return [
            new UuidColumn('id', null),
            new StringColumn('slug', false),
            new StringColumn('path', false),
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