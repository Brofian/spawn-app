<?php

namespace SpawnCore\Defaults\Database\SnippetTable;

use SpawnCore\System\Database\Entity\TableDefinition\AbstractTable;
use SpawnCore\System\Database\Entity\TableDefinition\DefaultColumns\CreatedAtColumn;
use SpawnCore\System\Database\Entity\TableDefinition\DefaultColumns\StringColumn;
use SpawnCore\System\Database\Entity\TableDefinition\DefaultColumns\UpdatedAtColumn;
use SpawnCore\System\Database\Entity\TableDefinition\DefaultColumns\UuidColumn;
use SpawnCore\System\Database\Entity\TableDefinition\ForeignKey;

class SnippetTable extends AbstractTable {

    public const TABLE_NAME = 'spawn_snippets';

    public function getTableColumns(): array
    {
        return [
            new UuidColumn('id', null),
            new UuidColumn('languageId', new ForeignKey('spawn_language', 'id', true, true)),
            new StringColumn('path', false, 'text', 'languageId', 1024),
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