<?php

namespace spawnApp\Database\MigrationTable;

use spawnCore\Database\Entity\TableDefinition\DefaultColumns\CreatedAtColumn;
use spawnCore\Database\Entity\TableDefinition\DefaultColumns\IntColumn;
use spawnCore\Database\Entity\TableDefinition\DefaultColumns\StringColumn;
use spawnCore\Database\Entity\TableDefinition\DefaultColumns\UpdatedAtColumn;
use spawnCore\Database\Entity\TableDefinition\DefaultColumns\UuidColumn;
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