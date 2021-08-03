<?php

namespace spawnApp\Database\SeoUrlTable;

use spawn\Core\Base\Database\Definition\TableDefinition\DefaultColumns\BooleanColumn;
use spawn\Core\Base\Database\Definition\TableDefinition\DefaultColumns\CreatedAtColumn;
use spawn\Core\Base\Database\Definition\TableDefinition\DefaultColumns\StringColumn;
use spawn\Core\Base\Database\Definition\TableDefinition\DefaultColumns\UpdatedAtColumn;
use spawn\Core\Base\Database\Definition\TableDefinition\DefaultColumns\UuidColumn;
use spawn\system\Core\Base\Database\Definition\TableDefinition\AbstractTable;

class SeoUrlTable extends AbstractTable {

    public const TABLE_NAME = 'spawn_seo_urls';

    public function getTableColumns(): array
    {
        return [
            new UuidColumn('id', null),
            new StringColumn('cUrl', false, null, true),
            new StringColumn('controller', false, 'system.fallback.404'),
            new StringColumn('action', false, 'error404Action'),
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