<?php declare(strict_types = 1);

namespace SpawnCore\Defaults\Database\UserTable;

use SpawnCore\System\Database\Entity\TableDefinition\AbstractTable;
use SpawnCore\System\Database\Entity\TableDefinition\DefaultColumns\BooleanColumn;
use SpawnCore\System\Database\Entity\TableDefinition\DefaultColumns\CreatedAtColumn;
use SpawnCore\System\Database\Entity\TableDefinition\DefaultColumns\DateTimeColumn;
use SpawnCore\System\Database\Entity\TableDefinition\DefaultColumns\StringColumn;
use SpawnCore\System\Database\Entity\TableDefinition\DefaultColumns\UpdatedAtColumn;
use SpawnCore\System\Database\Entity\TableDefinition\DefaultColumns\UuidColumn;

class UserTable extends AbstractTable {

    public const TABLE_NAME = 'spawn_user';

    public function getTableColumns(): array
    {
        return [
            new UuidColumn('id', null),
            new StringColumn('username', false, '', true, 750),
            new StringColumn('password', false, '', false, 1024),
            new StringColumn('email', false, '', true, 750),
            new BooleanColumn('active', true),
            new StringColumn('loginHash', true, null, true, 1024),
            new DateTimeColumn('lastLogin', true),
            new UpdatedAtColumn(),
            new CreatedAtColumn()
        ];
    }

    public function getTableName(): string
    {
        return self::TABLE_NAME;
    }
}