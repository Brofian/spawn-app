<?php

namespace spawnApp\Database\ConfigurationTable;

use spawnCore\Database\Entity\TableDefinition\DefaultColumns\BooleanColumn;
use spawnCore\Database\Entity\TableDefinition\DefaultColumns\CreatedAtColumn;
use spawnCore\Database\Entity\TableDefinition\DefaultColumns\DateTimeColumn;
use spawnCore\Database\Entity\TableDefinition\DefaultColumns\JsonColumn;
use spawnCore\Database\Entity\TableDefinition\DefaultColumns\StringColumn;
use spawnCore\Database\Entity\TableDefinition\DefaultColumns\UpdatedAtColumn;
use spawnCore\Database\Entity\TableDefinition\DefaultColumns\UuidColumn;
use spawnCore\Database\Entity\TableDefinition\AbstractTable;

class ConfigurationTable extends AbstractTable {

    public const TABLE_NAME = 'spawn_configuration';

    public function getTableColumns(): array
    {
        return [
            new UuidColumn('id', null),
            new StringColumn('internalName', false, '', true, 1024),
            new StringColumn('type', false, '', false, 1024),
            new JsonColumn('definition', false),
            new StringColumn('folder', false, 'global', false, 1024),
            new UpdatedAtColumn(),
            new CreatedAtColumn()
        ];
    }

    public function getTableName(): string
    {
        return self::TABLE_NAME;
    }
}