<?php

namespace spawnApp\Database\MigrationTable;

use spawn\system\Core\Base\Database\Definition\TableRepository;

class MigrationRepository extends TableRepository {

    public static function getEntityClass(): string
    {
        return MigrationEntity::class;
    }
}