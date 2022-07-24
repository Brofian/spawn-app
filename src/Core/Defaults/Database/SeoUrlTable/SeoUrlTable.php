<?php declare(strict_types = 1);
namespace SpawnCore\Defaults\Database\SeoUrlTable;

use SpawnCore\Defaults\Database\AnalysisTable\AnalysisTable;
use SpawnCore\System\Database\Entity\TableDefinition\AbstractTable;
use SpawnCore\System\Database\Entity\TableDefinition\Association\ToOneAssociation;
use SpawnCore\System\Database\Entity\TableDefinition\DefaultColumns\BooleanColumn;
use SpawnCore\System\Database\Entity\TableDefinition\DefaultColumns\CreatedAtColumn;
use SpawnCore\System\Database\Entity\TableDefinition\DefaultColumns\JsonColumn;
use SpawnCore\System\Database\Entity\TableDefinition\DefaultColumns\StringColumn;
use SpawnCore\System\Database\Entity\TableDefinition\DefaultColumns\UpdatedAtColumn;
use SpawnCore\System\Database\Entity\TableDefinition\DefaultColumns\UuidColumn;

class SeoUrlTable extends AbstractTable {

    public const ENTITY_NAME = 'spawn_seo_urls';

    public function getEntityClass(): string
    {
        return SeoUrlEntity::class;
    }

    public function getTableColumns(): array
    {
        return [
            new UuidColumn('id', null),
            new StringColumn('name', true, null, true, 750),
            new StringColumn('cUrl', false, null),
            new StringColumn('controller', false, 'system.fallback.404'),
            new StringColumn('action', false, 'error404Action'),
            new JsonColumn('parameters', true),
            new BooleanColumn('locked', false),
            new BooleanColumn('active', true),
            new BooleanColumn('requiresAdmin', false),
            new BooleanColumn('requiresUser', false),
            new BooleanColumn('api', false),
            new CreatedAtColumn(),
            new UpdatedAtColumn()
        ];
    }

    public function getTableAssociations(): array
    {
        return [
            new ToOneAssociation('id', AnalysisTable::ENTITY_NAME, 'urlId')
        ];
    }

    public function getRequiredColumns(): array {
        return [
            'id',
        ];
    }

}