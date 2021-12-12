<?php

namespace spawnApp\Database\CronTable;

use spawnCore\Database\Entity\TableDefinition\DefaultColumns\BooleanColumn;
use spawnCore\Database\Entity\TableDefinition\DefaultColumns\CreatedAtColumn;
use spawnCore\Database\Entity\TableDefinition\DefaultColumns\DateTimeColumn;
use spawnCore\Database\Entity\TableDefinition\DefaultColumns\StringColumn;
use spawnCore\Database\Entity\TableDefinition\DefaultColumns\UpdatedAtColumn;
use spawnCore\Database\Entity\TableDefinition\DefaultColumns\UuidColumn;
use spawnCore\Database\Entity\TableDefinition\AbstractTable;
use spawnCore\Cron\CronStates;

class CronTable extends AbstractTable {

    public const TABLE_NAME = 'spawn_crons';

    public function getTableColumns(): array
    {
        return [
            new UuidColumn('id', null),
            new StringColumn('action', false, null, false, 255, true),
            new StringColumn('result', true, ''),
            new StringColumn('state', false, CronStates::DEFAULT_STATE, false, 255, true),
            new UpdatedAtColumn(),
            new CreatedAtColumn()
        ];
    }

    public function getTableName(): string
    {
        return self::TABLE_NAME;
    }
}