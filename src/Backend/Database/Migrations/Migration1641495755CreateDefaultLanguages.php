<?php

namespace SpawnBackend\Database\Migrations;

use SpawnCore\Defaults\Database\LanguageTable\LanguageEntity;
use SpawnCore\Defaults\Database\LanguageTable\LanguageRepository;
use SpawnCore\Defaults\Database\LanguageTable\LanguageTable;
use SpawnCore\System\Custom\FoundationStorage\AbstractMigration;
use SpawnCore\System\Database\Helpers\DatabaseHelper;

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
        
        