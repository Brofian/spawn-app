<?php

namespace SpawnCore\Defaults\Database\LanguageTable;

use SpawnCore\System\Database\Entity\TableDefinition\AbstractTable;
use SpawnCore\System\Database\Entity\TableDefinition\DefaultColumns\CreatedAtColumn;
use SpawnCore\System\Database\Entity\TableDefinition\DefaultColumns\StringColumn;
use SpawnCore\System\Database\Entity\TableDefinition\DefaultColumns\UpdatedAtColumn;
use SpawnCore\System\Database\Entity\TableDefinition\DefaultColumns\UuidColumn;
use SpawnCore\System\Database\Entity\TableDefinition\ForeignKey;

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