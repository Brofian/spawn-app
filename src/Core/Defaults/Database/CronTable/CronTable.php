<?php declare(strict_types = 1);
namespace SpawnCore\Defaults\Database\CronTable;

use SpawnCore\System\Cron\CronStates;
use SpawnCore\System\Database\Entity\TableDefinition\AbstractTable;
use SpawnCore\System\Database\Entity\TableDefinition\DefaultColumns\CreatedAtColumn;
use SpawnCore\System\Database\Entity\TableDefinition\DefaultColumns\StringColumn;
use SpawnCore\System\Database\Entity\TableDefinition\DefaultColumns\UpdatedAtColumn;
use SpawnCore\System\Database\Entity\TableDefinition\DefaultColumns\UuidColumn;

class CronTable extends AbstractTable {

    public const ENTITY_NAME = 'crons';

    public function getEntityClass(): string
    {
        return CronEntity::class;
    }

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

    public function getRequiredColumns(): array {
        return [
            'id',
        ];
    }

}