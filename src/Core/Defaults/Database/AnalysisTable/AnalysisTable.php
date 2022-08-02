<?php declare(strict_types = 1);

namespace SpawnCore\Defaults\Database\AnalysisTable;

use SpawnCore\Defaults\Database\SeoUrlTable\SeoUrlTable;
use SpawnCore\System\Database\Entity\TableDefinition\AbstractTable;
use SpawnCore\System\Database\Entity\TableDefinition\Association\ToOneAssociation;
use SpawnCore\System\Database\Entity\TableDefinition\DefaultColumns\BooleanColumn;
use SpawnCore\System\Database\Entity\TableDefinition\DefaultColumns\CreatedAtColumn;
use SpawnCore\System\Database\Entity\TableDefinition\DefaultColumns\IntColumn;
use SpawnCore\System\Database\Entity\TableDefinition\DefaultColumns\JsonColumn;
use SpawnCore\System\Database\Entity\TableDefinition\DefaultColumns\StringColumn;
use SpawnCore\System\Database\Entity\TableDefinition\DefaultColumns\UuidColumn;
use SpawnCore\System\Database\Entity\TableDefinition\ForeignKey;

class AnalysisTable extends AbstractTable {

    public const ENTITY_NAME = 'analysis';

    public function getEntityClass(): string
    {
        return AnalysisEntity::class;
    }

    public function getTableColumns(): array
    {
        return [
            new UuidColumn('id', null),
            new UuidColumn('urlId', new ForeignKey(SeoUrlTable::ENTITY_NAME, 'id', true, false)),
            new StringColumn('ipHash', true, '', 'urlId', 750),
            new JsonColumn('data', true),
            new BooleanColumn('bot', true),
            new IntColumn('count', IntColumn::DEFAULT_INT, false, 1, false, true),
            new CreatedAtColumn()
        ];
    }

    public function getTableAssociations(): array
    {
        return [
            new ToOneAssociation('urlId', SeoUrlTable::ENTITY_NAME, 'id')
        ];
    }

    public function getRequiredColumns(): array {
        return [
            'id',
        ];
    }

}