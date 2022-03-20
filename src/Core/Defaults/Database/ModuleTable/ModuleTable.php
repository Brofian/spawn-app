<?php declare(strict_types = 1);
namespace SpawnCore\Defaults\Database\ModuleTable;

use SpawnCore\System\Database\Entity\TableDefinition\AbstractTable;
use SpawnCore\System\Database\Entity\TableDefinition\DefaultColumns\BooleanColumn;
use SpawnCore\System\Database\Entity\TableDefinition\DefaultColumns\CreatedAtColumn;
use SpawnCore\System\Database\Entity\TableDefinition\DefaultColumns\JsonColumn;
use SpawnCore\System\Database\Entity\TableDefinition\DefaultColumns\StringColumn;
use SpawnCore\System\Database\Entity\TableDefinition\DefaultColumns\UpdatedAtColumn;
use SpawnCore\System\Database\Entity\TableDefinition\DefaultColumns\UuidColumn;

class ModuleTable extends AbstractTable {

    public const TABLE_NAME = 'spawn_modules';

    public function getTableColumns(): array
    {
        return [
            new UuidColumn('id', null),
            new StringColumn('slug', false),
            new StringColumn('path', false),
            new BooleanColumn('active', false),
            new JsonColumn('information'),
            new JsonColumn('resourceConfig'),
            new CreatedAtColumn(),
            new UpdatedAtColumn()
        ];
    }

    public function getTableName(): string
    {
        return self::TABLE_NAME;
    }
}