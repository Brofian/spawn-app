<?php

namespace SpawnCore\Defaults\Database\LanguageTable;

use SpawnCore\Defaults\Database\SnippetTable\SnippetTable;
use SpawnCore\Defaults\Database\UserTable\UserTable;
use SpawnCore\System\Database\Entity\TableDefinition\AbstractTable;
use SpawnCore\System\Database\Entity\TableDefinition\Association\ToManyAssociation;
use SpawnCore\System\Database\Entity\TableDefinition\Association\ToOneAssociation;
use SpawnCore\System\Database\Entity\TableDefinition\DefaultColumns\CreatedAtColumn;
use SpawnCore\System\Database\Entity\TableDefinition\DefaultColumns\StringColumn;
use SpawnCore\System\Database\Entity\TableDefinition\DefaultColumns\UpdatedAtColumn;
use SpawnCore\System\Database\Entity\TableDefinition\DefaultColumns\UuidColumn;
use SpawnCore\System\Database\Entity\TableDefinition\ForeignKey;

class LanguageTable extends AbstractTable {

    public const ENTITY_NAME = 'language';

    public function getEntityClass(): string
    {
        return LanguageEntity::class;
    }

    public function getTableColumns(): array
    {
        return [
            new UuidColumn('id', null),
            new StringColumn('short', false, '', true, 750, false),
            new UuidColumn('parentId', new ForeignKey(self::ENTITY_NAME, 'id', true, false)),
            new UpdatedAtColumn(),
            new CreatedAtColumn()
        ];
    }

    public function getTableAssociations(): array
    {
        return [
            new ToManyAssociation('id', SnippetTable::ENTITY_NAME, 'languageId'),
            new ToOneAssociation('id', self::ENTITY_NAME, 'parentId'),
            new ToManyAssociation('parentId', self::ENTITY_NAME, 'id'),
            new ToManyAssociation('id', UserTable::ENTITY_NAME, 'languageId')
        ];
    }

    public function getRequiredColumns(): array {
        return [
            'id',
            'short',
        ];
    }
}