<?php

namespace spawnApp\Database;

use spawn\Core\Base\Database\Definition\TableDefinition\DefaultColumns\IntColumn;
use spawn\Core\Base\Database\Definition\TableDefinition\DefaultColumns\StringColumn;
use spawn\system\Core\Base\Database\Definition\TableDefinition\AbstractTable;

class DBALTest extends AbstractTable {

    function getTableColumns(): array
    {
        return [
            new IntColumn('ID', IntColumn::DEFAULT_INT, null, null, true, false),
            new StringColumn('text', null, 'hello world', null, null),
        ];
    }

    function getTableName(): string
    {
        return 'dbal_test';
    }
}