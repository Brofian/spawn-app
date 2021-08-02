<?php

namespace spawnApp\Database\ModuleTable;

use spawn\system\Core\Base\Database\Definition\TableRepository;

class ModuleRepository extends TableRepository {

    public static function getEntityClass(): string
    {
        return ModuleEntity::class;
    }
}