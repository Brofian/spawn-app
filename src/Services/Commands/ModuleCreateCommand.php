<?php

namespace spawnApp\Services\Commands;

use bin\spawn\IO;
use spawn\system\Core\Custom\AbstractCommand;

class ModuleCreateCommand extends AbstractCommand {

    public static function getCommand(): string
    {
        return 'modules:create';
    }

    public static function getShortDescription(): string
    {
        return 'A shortcut for creating a new module with default files';
    }

    protected static function getParameters(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function execute(array $parameters): int
    {
        IO::printError('TODO: Implement this command');

        return 0;
    }
}

/*

$moduleFolder = ROOT . "/custom/modules";
$existingModules = scandir($moduleFolder);

$answer = IO::readLine("Gib einen Namen für das neue Modul ein: ", function(&$answer) use ($existingModules) {
    $answer = trim($answer);
    if(strpos($answer, " ") !== false) return false;
    if(in_array($answer, $existingModules)) return false;

    return true;
},"Der Name ist ungültig oder existiert schon! Denk daran keine Leerzeichen zu benutzen!");

$answer = trim($answer);

//create folder structure
$moduleName = StringConverter::pascalToSnakeCase($answer);
$newModulePath = URIHelper::joinPaths($moduleFolder, $moduleName);


FileEditor::createFolder($newModulePath);

$newModuleSrcPath = URIHelper::joinPaths($newModulePath, "src");
FileEditor::createFolder($newModuleSrcPath);

FileEditor::createFolder(URIHelper::joinMultiplePaths($newModuleSrcPath, "Resources"));
FileEditor::createFolder(URIHelper::joinMultiplePaths($newModuleSrcPath, "Resources", "public"));
FileEditor::createFolder(URIHelper::joinMultiplePaths($newModuleSrcPath, "Resources", "public", "assets"));
FileEditor::createFolder(URIHelper::joinMultiplePaths($newModuleSrcPath, "Resources", "public", "js"));
FileEditor::createFolder(URIHelper::joinMultiplePaths($newModuleSrcPath, "Resources", "public", "scss"));
FileEditor::createFolder(URIHelper::joinMultiplePaths($newModuleSrcPath, "Resources", "template"));
FileEditor::createFolder(URIHelper::joinMultiplePaths($newModuleSrcPath, "Controller"));
FileEditor::createFolder(URIHelper::joinMultiplePaths($newModuleSrcPath, "Database"));
FileEditor::createFolder(URIHelper::joinMultiplePaths($newModuleSrcPath, "Database", "Migrations"));
FileEditor::createFolder(URIHelper::joinMultiplePaths($newModuleSrcPath, "Models"));



//create files
$slug = ModuleLoader::moduleLocationToSlug("custom", $moduleName);

FileEditor::createFile(URIHelper::joinMultiplePaths($newModulePath, "plugin.xml"),
    "<?xml version='1.0' encoding='UTF-8' ?>

<plugin>
    <resources weight='1' namespace='".$slug."'>/src/Resources</resources>
</plugin>
");

$moduleClassName = StringConverter::snakeToPascalCase($moduleName);
FileEditor::createFile(URIHelper::joinMultiplePaths($newModulePath, $moduleClassName.".php"),
    "<?php

namespace ".$slug.";

use spawn\system\Core\Base\Module\BaseModule;

class ".$moduleClassName." extends BaseModule {
}
");


IO::print("> Successfully created Module \"", IO::GREEN_TEXT);
IO::print($moduleName, IO::YELLOW_TEXT);
IO::print("\" in \"", IO::GREEN_TEXT);
IO::print($newModulePath, IO::YELLOW_TEXT);
IO::printLine("\"!", IO::GREEN_TEXT);

 */