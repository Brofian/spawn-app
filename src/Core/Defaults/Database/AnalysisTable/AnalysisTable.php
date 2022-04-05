<?php declare(strict_types = 1);

namespace SpawnCore\Defaults\Database\AnalysisTable;

use SpawnCore\Defaults\Database\SeoUrlTable\SeoUrlTable;
use SpawnCore\System\Database\Entity\TableDefinition\AbstractTable;
use SpawnCore\System\Database\Entity\TableDefinition\DefaultColumns\BooleanColumn;
use SpawnCore\System\Database\Entity\TableDefinition\DefaultColumns\CreatedAtColumn;
use SpawnCore\System\Database\Entity\TableDefinition\DefaultColumns\IntColumn;
use SpawnCore\System\Database\Entity\TableDefinition\DefaultColumns\JsonColumn;
use SpawnCore\System\Database\Entity\TableDefinition\DefaultColumns\StringColumn;
use SpawnCore\System\Database\Entity\TableDefinition\DefaultColumns\UuidColumn;
use SpawnCore\System\Database\Entity\TableDefinition\ForeignKey;

class AnalysisTable extends AbstractTable {

    public const TABLE_NAME = 'spawn_analysis';

    public function getTableColumns(): array
    {
        return [
            new UuidColumn('id', null),
            new UuidColumn('urlId', new ForeignKey(SeoUrlTable::TABLE_NAME, 'id', true, false)),
            new StringColumn('ipHash', true, '', 'urlId', 750),
            new JsonColumn('data', true),
            new BooleanColumn('bot', true),
            new IntColumn('count', IntColumn::DEFAULT_INT, false, 1, false, true),
            new CreatedAtColumn()
        ];
    }

    public function getTableName(): string
    {
        return self::TABLE_NAME;
    }
}