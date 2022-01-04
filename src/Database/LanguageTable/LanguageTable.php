<?php

namespace spawnApp\Database\LanguageTable;

use spawnCore\Database\Entity\TableDefinition\DefaultColumns\BooleanColumn;
use spawnCore\Database\Entity\TableDefinition\DefaultColumns\CreatedAtColumn;
use spawnCore\Database\Entity\TableDefinition\DefaultColumns\DateTimeColumn;
use spawnCore\Database\Entity\TableDefinition\DefaultColumns\JsonColumn;
use spawnCore\Database\Entity\TableDefinition\DefaultColumns\StringColumn;
use spawnCore\Database\Entity\TableDefinition\DefaultColumns\UpdatedAtColumn;
use spawnCore\Database\Entity\TableDefinition\DefaultColumns\UuidColumn;
use spawnCore\Database\Entity\TableDefinition\AbstractTable;
use spawnCore\Database\Entity\TableDefinition\ForeignKey;

class LanguageTable extends AbstractTable {

    public const TABLE_NAME = 'spawn_language';

    public function getTableColumns(): array
    {
        return [
            new UuidColumn('id', null),
            new StringColumn('short', false, '', true, 1024, false),
            new UuidColumn('parentId', new ForeignKey(self::TABLE_NAME, 'id', true, false)),
            new UpdatedAtColumn(),
            new CreatedAtColumn()
        ];
    }

    public function getTableName(): string
    {
        return self::TABLE_NAME;
    }
}