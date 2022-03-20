<?php declare(strict_types = 1);
namespace SpawnCore\Defaults\Database\SeoUrlTable;

use SpawnCore\System\Database\Entity\TableDefinition\AbstractTable;
use SpawnCore\System\Database\Entity\TableDefinition\DefaultColumns\BooleanColumn;
use SpawnCore\System\Database\Entity\TableDefinition\DefaultColumns\CreatedAtColumn;
use SpawnCore\System\Database\Entity\TableDefinition\DefaultColumns\JsonColumn;
use SpawnCore\System\Database\Entity\TableDefinition\DefaultColumns\StringColumn;
use SpawnCore\System\Database\Entity\TableDefinition\DefaultColumns\UpdatedAtColumn;
use SpawnCore\System\Database\Entity\TableDefinition\DefaultColumns\UuidColumn;

class SeoUrlTable extends AbstractTable {

    public const TABLE_NAME = 'spawn_seo_urls';

    public function getTableColumns(): array
    {
        return [
            new UuidColumn('id', null),
            new StringColumn('cUrl', false, null),
            new StringColumn('controller', false, 'system.fallback.404'),
            new StringColumn('action', false, 'error404Action'),
            new JsonColumn('parameters', true),
            new BooleanColumn('locked', false),
            new BooleanColumn('active', true),
            new CreatedAtColumn(),
            new UpdatedAtColumn()
        ];
    }

    public function getTableName(): string
    {
        return self::TABLE_NAME;
    }
}