<?php declare(strict_types = 1);

namespace SpawnCore\Defaults\Database\UserTable;

use SpawnCore\Defaults\Database\LanguageTable\LanguageRepository;
use SpawnCore\Defaults\Database\LanguageTable\LanguageTable;
use SpawnCore\System\Database\Entity\TableDefinition\AbstractTable;
use SpawnCore\System\Database\Entity\TableDefinition\Association\ToManyAssociation;
use SpawnCore\System\Database\Entity\TableDefinition\Association\ToOneAssociation;
use SpawnCore\System\Database\Entity\TableDefinition\DefaultColumns\BooleanColumn;
use SpawnCore\System\Database\Entity\TableDefinition\DefaultColumns\CreatedAtColumn;
use SpawnCore\System\Database\Entity\TableDefinition\DefaultColumns\DateTimeColumn;
use SpawnCore\System\Database\Entity\TableDefinition\DefaultColumns\StringColumn;
use SpawnCore\System\Database\Entity\TableDefinition\DefaultColumns\UpdatedAtColumn;
use SpawnCore\System\Database\Entity\TableDefinition\DefaultColumns\UuidColumn;
use SpawnCore\System\Database\Entity\TableDefinition\ForeignKey;
use spawnWebsite\Database\UserTodoTable\UserTodoTable;

class UserTable extends AbstractTable {

    public const ENTITY_NAME = 'spawn_user';

    public function getEntityClass(): string
    {
        return UserEntity::class;
    }

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
            new UuidColumn('languageId', new ForeignKey(LanguageTable::ENTITY_NAME, 'id', false, false), false),
            new UpdatedAtColumn(),
            new CreatedAtColumn()
        ];
    }

    public function getTableAssociations(): array
    {
        return [
            new ToManyAssociation('id', UserTodoTable::ENTITY_NAME, 'userId'),
            new ToOneAssociation('languageId', LanguageTable::ENTITY_NAME, 'id'),
        ];
    }

    public function getRequiredColumns(): array {
        return [
            'id',
        ];
    }

}