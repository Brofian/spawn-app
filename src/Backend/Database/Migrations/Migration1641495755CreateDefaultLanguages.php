<?php

namespace SpawnBackend\Database\Migrations;

use bin\spawn\IO;
use Doctrine\DBAL\Exception;
use SpawnCore\Defaults\Database\LanguageTable\LanguageEntity;
use SpawnCore\Defaults\Database\LanguageTable\LanguageRepository;
use SpawnCore\Defaults\Database\LanguageTable\LanguageTable;
use SpawnCore\System\Custom\FoundationStorage\AbstractMigration;
use SpawnCore\System\Custom\Throwables\DatabaseConnectionException;
use SpawnCore\System\Custom\Throwables\WrongEntityForRepositoryException;
use SpawnCore\System\Database\Helpers\DatabaseHelper;

class Migration1641495755CreateDefaultLanguages extends AbstractMigration {
    
    public static function getUnixTimestamp(): int
    {
        //Do not edit this!
        return 1641495755;
    }

    /**
     * @throws Exception
     * @throws DatabaseConnectionException
     * @throws WrongEntityForRepositoryException
     */
    public function run(DatabaseHelper $dbHelper)
    {
        $repository = new LanguageRepository(new LanguageTable());

        $langEN = new LanguageEntity('EN', null);
        $langDE = new LanguageEntity('DE', $langEN->getId());

        try {
            $repository->upsert($langEN);
            $repository->upsert($langDE);
        }
        catch (\Throwable $t) {
            IO::printError($t->getMessage());
        }

    }

}        
        
        