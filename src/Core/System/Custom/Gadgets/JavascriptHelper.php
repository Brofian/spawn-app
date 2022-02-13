<?php

namespace SpawnCore\System\Custom\Gadgets;

use bin\spawn\IO;
use Doctrine\DBAL\Exception;
use RuntimeException;
use SpawnCore\Defaults\Commands\ListModulesCommand;
use SpawnCore\Defaults\Commands\NpmInstallCommand;
use SpawnCore\System\CardinalSystem\ModuleNetwork\ModuleNamespacer;
use SpawnCore\System\Custom\Throwables\DatabaseConnectionException;
use SpawnCore\System\Database\Entity\RepositoryException;

class JavascriptHelper
{
    public const WEBPACK_CONFIG_FILE    = ROOT . '/src/npm/webpack.config.js';
    public const MODULE_FILES_CACHE     = ROOT . '/var/cache/resources/modules/js';
    public const OUTPUT_PATH            = ROOT . '/public/cache';

    protected const COMPILE_COMMAND_PATTERN = "npx webpack --entry %s --output-path %s --config %s --progress";


    protected bool $executedNpmInstallCommand = false;

    protected function prepareCompilation(): void {
        if($this->executedNpmInstallCommand) {
            return;
        }
        NpmInstallCommand::addNodeJSToPath();
    }


    /**
     * @throws Exception
     * @throws DatabaseConnectionException
     * @throws RepositoryException
     */
    public function compileAll(?string $namespaceLimitation = null): void {
        $this->prepareCompilation();

        $configFilePath = self::WEBPACK_CONFIG_FILE;
        $webpackDir = dirname($configFilePath);

        $moduleCollection = ListModulesCommand::getModuleList();
        $namespaces = NamespaceHelper::getNamespacesFromModuleCollection($moduleCollection);


        foreach($namespaces as $namespace => $moduleList) {
            if($namespaceLimitation && $namespaceLimitation !== $namespace) {
                continue;
            }

            $this->compileNamespace($namespace, $configFilePath, $webpackDir);
        }
    }

    protected function compileNamespace(string $namespace, string $configFilePath, string $webpackDirectory): void {
        $entryFile = self::MODULE_FILES_CACHE . '/'.$namespace.'_index.js';

        if (file_exists($entryFile)) {
            $outputPath = self::OUTPUT_PATH . '/' . ModuleNamespacer::hashNamespace($namespace) . '/js';

            $command = sprintf(
                self::COMPILE_COMMAND_PATTERN,
                $entryFile, // the entry file path
                $outputPath, // the output file path
                $configFilePath // the config file path
            );

            $output = IO::execInDir($command, $webpackDirectory, false, $result, $code);

            if($code !== 0) {
                if(is_string($result)) {
                    $result = [$result];
                }
                IO::printError(implode(PHP_EOL, $result));

                throw new RuntimeException('Could not compile javascript with webpack');
            }

            IO::printLine(IO::TAB . '- ' . $namespace, '', 1);
            IO::printLine(IO::TAB . '   > ' . $output, '', 2);
        }
    }



}