<?php

namespace SpawnCore\Defaults\Database\MigrationTable;

use SpawnCore\System\Database\Entity\TableDefinition\AbstractTable;
use SpawnCore\System\Database\Entity\TableDefinition\DefaultColumns\CreatedAtColumn;
use SpawnCore\System\Database\Entity\TableDefinition\DefaultColumns\IntColumn;
use SpawnCore\System\Database\Entity\TableDefinition\DefaultColumns\StringColumn;
use SpawnCore\System\Database\Entity\TableDefinition\DefaultColumns\UpdatedAtColumn;
use SpawnCore\System\Database\Entity\TableDefinition\DefaultColumns\UuidColumn;

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