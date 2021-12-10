<?php

namespace spawnApp\Database\CronTable;

use spawn\Core\Base\Database\Definition\TableDefinition\DefaultColumns\BooleanColumn;
use spawn\Core\Base\Database\Definition\TableDefinition\DefaultColumns\CreatedAtColumn;
use spawn\Core\Base\Database\Definition\TableDefinition\DefaultColumns\DateTimeColumn;
use spawn\Core\Base\Database\Definition\TableDefinition\DefaultColumns\StringColumn;
use spawn\Core\Base\Database\Definition\TableDefinition\DefaultColumns\UpdatedAtColumn;
use spawn\Core\Base\Database\Definition\TableDefinition\DefaultColumns\UuidColumn;
use spawn\system\Core\Base\Database\Definition\TableDefinition\AbstractTable;

class CronTable extends AbstractTable {

    public const TABLE_NAME = 'spawn_crons';

    public function getTableColumns(): array
    {
        return [
            new UuidColumn('id', null),
            new StringColumn('action', false, null, false, 255, true),
            new StringColumn('result', true, ''),
            new StringColumn('state', false, 'open', false, 255, true),
            new UpdatedAtColumn(),
            new CreatedAtColumn()
        ];
    }

    public function getTableName(): string
    {
        return self::TABLE_NAME;
    }
}