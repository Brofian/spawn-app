<?php declare(strict_types = 1);
namespace SpawnCore\Defaults\Database\ConfigurationTable;

use SpawnCore\System\Database\Entity\TableDefinition\AbstractTable;
use SpawnCore\System\Database\Entity\TableDefinition\DefaultColumns\CreatedAtColumn;
use SpawnCore\System\Database\Entity\TableDefinition\DefaultColumns\JsonColumn;
use SpawnCore\System\Database\Entity\TableDefinition\DefaultColumns\StringColumn;
use SpawnCore\System\Database\Entity\TableDefinition\DefaultColumns\UpdatedAtColumn;
use SpawnCore\System\Database\Entity\TableDefinition\DefaultColumns\UuidColumn;

class ConfigurationTable extends AbstractTable {

    public const ENTITY_NAME = 'configuration';

    public function getEntityClass(): string
    {
        return ConfigurationEntity::class;
    }

    public function getTableColumns(): array
    {
        return [
            new UuidColumn('id', null),
            new StringColumn('internalName', false, '', true, 750),
            new StringColumn('type', false, 'text', false, 1024),
            new StringColumn('value', true, null, false),
            new JsonColumn('definition', false),
            new StringColumn('folder', false, 'global', false, 1024),
            new UpdatedAtColumn(),
            new CreatedAtColumn()
        ];
    }

    public function getRequiredColumns(): array {
        return [
            'id',
            'internalName',
        ];
    }

}