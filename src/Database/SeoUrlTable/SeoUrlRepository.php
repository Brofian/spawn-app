<?php

namespace spawnApp\Database\SeoUrlTable;

use spawn\system\Core\Base\Database\Definition\TableRepository;

class SeoUrlRepository extends TableRepository {

    public static function getEntityClass(): string
    {
        return SeoUrlEntity::class;
    }
}