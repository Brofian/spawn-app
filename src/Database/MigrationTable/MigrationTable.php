<?php

namespace spawnApp\Database\MigrationTable;

use spawn\Core\Base\Database\Definition\TableDefinition\DefaultColumns\CreatedAtColumn;
use spawn\Core\Base\Database\Definition\TableDefinition\DefaultColumns\IntColumn;
use spawn\Core\Base\Database\Definition\TableDefinition\DefaultColumns\StringColumn;
use spawn\Core\Base\Database\Definition\TableDefinition\DefaultColumns\UpdatedAtColumn;
use spawn\Core\Base\Database\Definition\TableDefinition\DefaultColumns\UuidColumn;
use spawnCore\Database\Entity\TableDefinition\AbstractTable;

class MigrationTable extends AbstractTable {

    public const TABLE_NAME = 'spawn_migrations';

    public function getTableColumns(): array
    {
        return [
            new UuidColumn('id', null),
            new StringColumn('class'),
            new IntColumn('timestamp', IntColumn::DEFAULT_INT, false),
            new CreatedAtColumn(),
            new UpdatedAtColumn()
        ];
    }

    public function getTableName(): string
    {
        return self::TABLE_NAME;
    }
}