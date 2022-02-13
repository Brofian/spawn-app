<?php

namespace SpawnCore\Defaults\Database\CronTable;

use SpawnCore\System\Database\Entity\TableDefinition\DefaultColumns\CreatedAtColumn;
use SpawnCore\System\Database\Entity\TableDefinition\DefaultColumns\StringColumn;
use SpawnCore\System\Database\Entity\TableDefinition\DefaultColumns\UpdatedAtColumn;
use SpawnCore\System\Database\Entity\TableDefinition\DefaultColumns\UuidColumn;
use SpawnCore\System\Database\Entity\TableDefinition\AbstractTable;
use SpawnCore\System\Cron\CronStates;

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