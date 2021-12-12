<?php

namespace spawnApp\Database\SeoUrlTable;

use spawnCore\Database\Entity\TableDefinition\DefaultColumns\BooleanColumn;
use spawnCore\Database\Entity\TableDefinition\DefaultColumns\CreatedAtColumn;
use spawnCore\Database\Entity\TableDefinition\DefaultColumns\JsonColumn;
use spawnCore\Database\Entity\TableDefinition\DefaultColumns\StringColumn;
use spawnCore\Database\Entity\TableDefinition\DefaultColumns\UpdatedAtColumn;
use spawnCore\Database\Entity\TableDefinition\DefaultColumns\UuidColumn;
use spawnCore\Database\Entity\TableDefinition\AbstractTable;

class SeoUrlTable extends AbstractTable {

    public const TABLE_NAME = 'spawn_seo_urls';

    public function getTableColumns(): array
    {
        return [
            new UuidColumn('id', null),
            new StringColumn('cUrl', false, null, true),
            new StringColumn('controller', false, 'system.fallback.404'),
            new StringColumn('action', false, 'error404Action'),
            new JsonColumn('parameters', true),
            new BooleanColumn('locked', false),
            new BooleanColumn('active', true),
            new CreatedAtColumn(),
            new UpdatedAtColumn()
        ];
    }

    public function getTableName(): string
    {
        return self::TABLE_NAME;
    }
}