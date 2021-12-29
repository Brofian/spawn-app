<?php

namespace spawnApp\Services\Commands;



use bin\spawn\IO;
use Exception;
use spawnApp\Database\ModuleTable\ModuleEntity;
use spawnCore\CardinalSystem\ModuleNetwork\ModuleNamespacer;
use spawnCore\Custom\FoundationStorage\AbstractCommand;
use spawnCore\Custom\Gadgets\NamespaceHelper;
use spawnCore\Custom\Gadgets\ResourceCollector;
use spawnCore\Custom\Gadgets\ScssHelper;
use spawnCore\Database\Entity\EntityCollection;

class ThemeCompileCommand extends AbstractCommand {

    public const WEBPACK_CONFIG_FILE = ROOT .'/src/npm/webpack.config.js';
    public const MODULE_FILES_CACHE = ROOT . '/var/cache/resources/modules/js';
    public const OUTPUT_PATH = ROOT . '/public/cache';

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

        $compileAll = !($parameters['js'] || $parameters['scss']);

        $this->gatherFiles($moduleCollection);

        if($parameters['scss'] || $compileAll) {
            $this->compileScss();
        }

        if($parameters['js'] || $compileAll) {
            $this->compileJavascript();
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
        //compile javascript
        IO::printWarning("> compiling JavaScript");

        NpmInstallCommand::addNodeJSToPath();
        $code = 0;
        $webpackDir = dirname(self::WEBPACK_CONFIG_FILE);
        $configPath = self::WEBPACK_CONFIG_FILE;;

        $moduleCollection = ListModulesCommand::getModuleList();
        $namespaces = NamespaceHelper::getNamespacesFromModuleCollection($moduleCollection);

        $output = '';
        foreach($namespaces as $namespace => $moduleList) {
            $entryFile = self::MODULE_FILES_CACHE . '/'.$namespace.'_index.js';


            if (file_exists($entryFile)) {
                $outputPath = self::OUTPUT_PATH . '/' . ModuleNamespacer::hashNamespace($namespace) . '/js';
                $command = "npx webpack --entry $entryFile --output-path $outputPath";

                $result = '';
                $output = IO::execInDir("$command --config $configPath  --progress", $webpackDir, false, $result, $code);

                if($code != 0) {
                    if(is_string($result)) {
                        $result = [$result];
                    }
                    IO::printError(implode(PHP_EOL, $result));

                    throw new Exception('Could not compile javascript with webpack');
                }

            }

            IO::printLine(IO::TAB . '- ' . $namespace, '', 1);
            IO::printLine(IO::TAB . '   > ' . $output, '', 2);
        }

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