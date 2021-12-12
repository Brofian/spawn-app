<?php

namespace spawnApp\Services\Commands;



use bin\spawn\IO;
use Exception;
use spawnApp\Database\ModuleTable\ModuleEntity;
use spawnCore\Custom\FoundationStorage\AbstractCommand;
use spawnCore\Custom\Gadgets\ResourceCollector;
use spawnCore\Custom\Gadgets\ScssHelper;
use spawnCore\Database\Entity\EntityCollection;

class ThemeCompileCommand extends AbstractCommand {

    public static function getCommand(): string
    {
        return 'modules:compile';
    }

    public static function getShortDescription(): string
    {
        return '';
    }

    public static function getParameters(): array
    {
        return [
            'js' => ['j', 'javascript'],
            'scss' => ['s', 'scss'],
        ];
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function execute(array $parameters): int
    {
        $moduleCollection = ListModulesCommand::getModuleList();

        $compileAll = (!!$parameters['js'] && !!$parameters['scss']);



        $this->gatherFiles($moduleCollection);

        if($parameters['js'] || $compileAll) {
            $this->compileJavascript();
        }

        if($parameters['scss'] || $compileAll) {
            $this->compileScss();
        }

        return 0;
    }


    protected function compileScss(): void {
        IO::printWarning("> compiling SCSS");

        $scssHelper = new ScssHelper();
        $scssHelper->setBaseVariable("assetsPath", MAIN_ADDRESS.'/'.CACHE_DIR.'/public/assets');
        $scssHelper->setBaseVariable("defaultAssetsPath", MAIN_ADDRESS.'/'.CACHE_DIR.'/public/assets');
        $scssHelper->createCss();

        IO::printSuccess("> - successfully compiled SCSS");
    }

    /**
     * @throws Exception
     */
    protected function compileJavascript(): void {
        //javascript kompilieren
        IO::printWarning("> compiling JavaScript");

        NpmInstallCommand::addNodeJSToPath();
        $code = 0;
        $webpackDir = ROOT . "/src/npm";
        $output = IO::execInDir("npx webpack --config webpack.config.js --progress", $webpackDir, false, $result, $code);

        if($code != 0) {
            IO::printError(implode(PHP_EOL, $result));
            throw new Exception('Could not compile javascript with webpack');
        }

        IO::printLine(IO::TAB . '- ' . $output);
        IO::printSuccess("> - successfully compiled JavaScript");
    }

    protected function gatherFiles(EntityCollection $moduleCollection): void {
        IO::printWarning("> gathering files from modules...");

        /** @var ModuleEntity $module */
        foreach($moduleCollection->getArray() as $module) {
            IO::printLine(IO::TAB . "- " . $module->getSlug());
        }
        $resourceCollector = new ResourceCollector();
        $resourceCollector->gatherModuleData($moduleCollection);

        IO::printSuccess("> - Successfully gathered files");
    }

}