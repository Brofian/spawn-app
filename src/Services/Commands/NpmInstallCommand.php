<?php

namespace spawnApp\Services\Commands;

use bin\spawn\IO;
use Exception;
use spawnCore\Custom\FoundationStorage\AbstractCommand;

class NpmInstallCommand extends AbstractCommand  {

    public static function getCommand(): string {
        return 'npm:install-dependencies';
    }

    public static function getShortDescription(): string    {
        return 'Installs or updates the dependencies for all npm actions';
    }

    public static function getParameters(): array   {
        return [];
    }

    public function execute(array $parameters): int  {

        try {
            // download nodejs, npm
            IO::execInDir('composer run-script download-nodejs', ROOT);

            self::addNodeJSToPath();

            //npx is installed by npm, which is installed as part of nodejs
            IO::execInDir('npm install npx', ROOT . "/src/npm");
            IO::execInDir("npm install ", ROOT . "/src/npm", true, $result);

        }
        catch (Exception $e) {
            return $e->getCode();
        }

        return 0;
    }

    public static function addNodeJSToPath(): void {
        $njsPath = ROOT."/vendor/nodejs/nodejs/bin";
        $path = getenv("PATH");
        if(strpos($path, $njsPath) === false) {
            putenv("PATH=$path:$njsPath");
            IO::printWarning("Added node JS to PATH");
        }
    }


}