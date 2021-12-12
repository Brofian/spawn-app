<?php

namespace spawnApp\Database\AdministratorTable;

use spawnCore\Database\Entity\TableDefinition\DefaultColumns\BooleanColumn;
use spawnCore\Database\Entity\TableDefinition\DefaultColumns\CreatedAtColumn;
use spawnCore\Database\Entity\TableDefinition\DefaultColumns\DateTimeColumn;
use spawnCore\Database\Entity\TableDefinition\DefaultColumns\StringColumn;
use spawnCore\Database\Entity\TableDefinition\DefaultColumns\UpdatedAtColumn;
use spawnCore\Database\Entity\TableDefinition\DefaultColumns\UuidColumn;
use spawnCore\Database\Entity\TableDefinition\AbstractTable;

class AdministratorTable extends AbstractTable {

    public const TABLE_NAME = 'spawn_administrator';

    public function getTableColumns(): array
    {
        return [
            new UuidColumn('id', null),
            new StringColumn('username', false, '', true, 1024),
            new StringColumn('password', false, '', false, 1024),
            new StringColumn('email', false, '', true, 1024),
            new BooleanColumn('active', true),
            new StringColumn('loginHash', true, null, false, '1024'),
            new DateTimeColumn('loginExpiration'),
            new UpdatedAtColumn(),
            new CreatedAtColumn()
        ];
    }

    public function getTableName(): string
    {
        return self::TABLE_NAME;
    }
}