<?php declare(strict_types = 1);
namespace SpawnCore\Defaults\Database\SnippetTable;

use SpawnCore\Defaults\Database\LanguageTable\LanguageTable;
use SpawnCore\System\Database\Entity\TableDefinition\AbstractTable;
use SpawnCore\System\Database\Entity\TableDefinition\Association\ToOneAssociation;
use SpawnCore\System\Database\Entity\TableDefinition\DefaultColumns\CreatedAtColumn;
use SpawnCore\System\Database\Entity\TableDefinition\DefaultColumns\StringColumn;
use SpawnCore\System\Database\Entity\TableDefinition\DefaultColumns\UpdatedAtColumn;
use SpawnCore\System\Database\Entity\TableDefinition\DefaultColumns\UuidColumn;
use SpawnCore\System\Database\Entity\TableDefinition\ForeignKey;

class SnippetTable extends AbstractTable {

    public const ENTITY_NAME = 'spawn_snippets';

    public function getEntityClass(): string
    {
        return SnippetEntity::class;
    }

    public function getTableColumns(): array
    {
        return [
            new UuidColumn('id', null),
            new UuidColumn('languageId', new ForeignKey(LanguageTable::ENTITY_NAME, 'id', true, true)),
            new StringColumn('path', false, 'text', 'languageId', 750),
            new StringColumn('value', true, null, false),
            new UpdatedAtColumn(),
            new CreatedAtColumn()
        ];
    }

    public function getTableAssociations(): array
    {
        return [
            new ToOneAssociation('languageId', LanguageTable::ENTITY_NAME, 'id')
        ];
    }

    public function getRequiredColumns(): array {
        return [
            'id',
        ];
    }

}