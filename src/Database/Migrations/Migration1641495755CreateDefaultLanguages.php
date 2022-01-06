<?php

namespace spawnApp\Database\Migrations;

use spawnApp\Database\LanguageTable\LanguageEntity;
use spawnApp\Database\LanguageTable\LanguageRepository;
use spawnApp\Database\LanguageTable\LanguageTable;
use spawnCore\Custom\FoundationStorage\AbstractMigration;
use spawnCore\Database\Helpers\DatabaseHelper;

class Migration1641495755CreateDefaultLanguages extends AbstractMigration {
    
    public static function getUnixTimestamp(): int
    {
        //Do not edit this!
        return 1641495755;
    }

    function run(DatabaseHelper $dbHelper)
    {
        $repository = new LanguageRepository(new LanguageTable());

        $langEN = new LanguageEntity('EN', null);
        $repository->upsert($langEN);

        $langDE = new LanguageEntity('DE', $langEN->getId());
        $repository->upsert($langDE);
    }

}        
        
        