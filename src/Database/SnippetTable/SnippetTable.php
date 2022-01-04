<?php

namespace spawnApp\Database\SnippetTable;

use spawnCore\Database\Entity\TableDefinition\DefaultColumns\BooleanColumn;
use spawnCore\Database\Entity\TableDefinition\DefaultColumns\CreatedAtColumn;
use spawnCore\Database\Entity\TableDefinition\DefaultColumns\DateTimeColumn;
use spawnCore\Database\Entity\TableDefinition\DefaultColumns\JsonColumn;
use spawnCore\Database\Entity\TableDefinition\DefaultColumns\StringColumn;
use spawnCore\Database\Entity\TableDefinition\DefaultColumns\UpdatedAtColumn;
use spawnCore\Database\Entity\TableDefinition\DefaultColumns\UuidColumn;
use spawnCore\Database\Entity\TableDefinition\AbstractTable;
use spawnCore\Database\Entity\TableDefinition\ForeignKey;

class SnippetTable extends AbstractTable {

    public const TABLE_NAME = 'spawn_snippets';

    public function getTableColumns(): array
    {
        return [
            new UuidColumn('id', null),
            new StringColumn('namespace', false, '', false, 1024),
            new StringColumn('path', false, 'text', false, 1024),
            new UuidColumn('languageId', new ForeignKey('spawn_language', 'id', true, true)),
            new StringColumn('value', true, null, false),
            new UpdatedAtColumn(),
            new CreatedAtColumn()
        ];
    }

    public function getTableName(): string
    {
        return self::TABLE_NAME;
    }
}