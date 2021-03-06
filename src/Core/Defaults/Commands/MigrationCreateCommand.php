<?php

namespace SpawnCore\Defaults\Commands;


use bin\spawn\IO;
use Doctrine\DBAL\Exception;
use SpawnCore\Defaults\Database\ModuleTable\ModuleEntity;
use SpawnCore\System\Custom\FoundationStorage\AbstractCommand;
use SpawnCore\System\Custom\Gadgets\FileEditor;
use SpawnCore\System\Custom\Throwables\DatabaseConnectionException;
use SpawnCore\System\Database\Entity\RepositoryException;

class MigrationCreateCommand extends AbstractCommand {


    public static function getCommand(): string
    {
        return 'database:migration:create';
    }

    public static function getShortDescription(): string
    {
        return 'Creates a default Migration for developers to fill with commands';
    }

    public static function getParameters(): array
    {
        return [
            'name' => ['n', 'name'],
            'module' => ['m', 'module']
        ];
    }

    /**
     * @throws Exception
     * @throws DatabaseConnectionException
     * @throws RepositoryException
     */
    public function execute(array $parameters): int
    {
        $moduleCollection = ListModulesCommand::getModuleList();

        if($parameters['module']) {
            $moduleSelector = $parameters['module'];

            $selectedModule = null;
            /** @var ModuleEntity $module */
            foreach($moduleCollection->getArray() as $module) {
                if($module->getSlug() === $moduleSelector || $module->getId() === $moduleSelector) {
                    $selectedModule = $module;
                    break;
                }
            }

            if($selectedModule === null) {
                IO::printError('The given ID is invalid!');
                return 1;
            }
            $module = $selectedModule;

        }
        else {
            IO::printLine("In which module should the migration be created?", IO::BLUE_TEXT);

            $counter = 0;
            $modules = [];

            /** @var ModuleEntity $module */
            foreach($moduleCollection->getArray() as $module) {
                IO::printLine("[".$counter."] " . $module->getSlug());
                $modules[$counter] = $module;
                $counter++;
            }

            $moduleId = IO::readLine("Insert a valid ID: ", static function($answer) use ($counter) {
                return (is_numeric($answer) && (int)$answer < $counter && (int)$answer >= 0);
            }, 'The given ID is invalid!', 1);

            if($moduleId === false) {
                return 1;
            }

            /** @var ModuleEntity $module */
            $module = $modules[$moduleId];
        }



        if($parameters['name']) {
            $name = $parameters['name'];
            if(strlen($name) <= 3 && strpos($name, '/') !== false) {
                IO::printError('The name has to be at least three characters long and not contain slashes!');
                return 2;
            }
        }
        else {
            $name = IO::readLine("How should the migration be called? ", static function ($answer) {
                return (
                    strlen($answer) > 3 &&
                    strpos($answer, '/') === false
                );
            }, 'The name has to be at least three characters long and not contain slashes!');
            if($name === false) {
                return 2;
            }
        }


        $path = $module->getPath() . '/src/Database/Migrations';
        $timeStamp = time();
        $className = "Migration".$timeStamp.$name;
        $filePath = ROOT.$path."/$className.php";
        $slug = $module->getSlug();
        FileEditor::createFile($filePath, $this->getMigrationFileContents($slug, $className, $timeStamp));

        return 0;
    }


    protected function getMigrationFileContents(string $slug, string $className, string $timestamp): string {
        return "<?php

namespace ".$slug."\\Database\\Migrations;

use SpawnCore\System\Custom\FoundationStorage\AbstractMigration;
use SpawnCore\System\Database\Helpers\DatabaseHelper;

class ".$className." extends AbstractMigration {
    
    public static function getUnixTimestamp(): int
    {
        //Do not edit this!
        return ".$timestamp.";
    }

    public function run(DatabaseHelper \$dbHelper)
    {
        //TODO: Add your Migration code here
    }

}        
        
        ";
    }


}