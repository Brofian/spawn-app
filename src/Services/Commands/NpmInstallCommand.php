<?php

namespace spawnApp\Services\Commands;

use bin\spawn\IO;
use spawn\system\Core\Custom\AbstractCommand;

class NpmInstallCommand extends AbstractCommand  {

    public static function getCommand(): string {
        return 'npm:install-dependencies';
    }

    public static function getShortDescription(): string    {
        return 'Installs or updates the dependencies for all npm actions';
    }

    protected static function getParameters(): array   {
        return [];
    }

    public function execute(array $parameters): int  {

        try {
            IO::execInDir('composer run-script download-nodejs', ROOT);

            self::addNodeJSToPath();

            //npx is installed as part of npm, which is installed as part of nodejs
            IO::execInDir('npm install -g --force npx', ROOT);
            IO::execInDir("npm install --force", ROOT . "/src/npm");
        }
        catch (\Exception $e) {
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