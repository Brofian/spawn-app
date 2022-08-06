<?php

namespace SpawnCore\Defaults\Commands;



use bin\spawn\IO;
use Exception;
use SpawnCore\Defaults\Database\ModuleTable\ModuleEntity;
use SpawnCore\Defaults\Events\JavascriptCompileEvent;
use SpawnCore\Defaults\Events\ScssCompileEvent;
use SpawnCore\System\Custom\FoundationStorage\AbstractCommand;
use SpawnCore\System\Custom\Gadgets\JavascriptHelper;
use SpawnCore\System\Custom\Gadgets\NamespaceHelper;
use SpawnCore\System\Custom\Gadgets\ResourceCollector;
use SpawnCore\System\Custom\Gadgets\ScssHelper;
use SpawnCore\System\Custom\Throwables\DatabaseConnectionException;
use SpawnCore\System\Database\Entity\EntityCollection;
use SpawnCore\System\Database\Entity\RepositoryException;
use SpawnCore\System\EventSystem\EventEmitter;

class ThemeCompileCommand extends AbstractCommand {

    protected array $parameters = [];

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
            'namespace' => ['n', 'namespace']
        ];
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function execute(array $parameters): int
    {
        $this->parameters = $parameters;
        $compileAll = !($parameters['js'] || $parameters['scss']);


        $moduleList = ListModulesCommand::getModuleList();
        if($parameters['namespace'] && !$this->isNamespaceLimitationValid($moduleList, $parameters['namespace'])) {
            $availableNamespaces = NamespaceHelper::getNamespacesFromModuleCollection($moduleList);
            IO::printError('The namespace "'.$parameters['namespace'].'" does not exist! Please use one of these options: ' . PHP_EOL . '- ' . implode(PHP_EOL.'- ', array_keys($availableNamespaces)));
            return 1;
        }

        $this->gatherFiles($moduleList);

        if($parameters['scss'] || $compileAll) {
            $this->compileScss();
        }

        if($parameters['js'] || $compileAll) {
            $this->compileJavascript();
        }

        return 0;
    }

    protected function isNamespaceLimitationValid(EntityCollection $moduleCollection, string $namespace): bool {
        /** @var ModuleEntity $moduleEntity */
        foreach($moduleCollection as $moduleEntity) {
           if($moduleEntity->getNamespace() === $namespace) {
               return true;
           }
        }
        return false;
    }


    /**
     * @throws \Doctrine\DBAL\Exception
     * @throws DatabaseConnectionException
     * @throws RepositoryException
     */
    protected function compileScss(): void {
        IO::printWarning("> compiling SCSS");

        $scssHelper = new ScssHelper();
        EventEmitter::get()->publish(new ScssCompileEvent($scssHelper));
        $scssHelper->createCss($this->parameters['namespace']);

        IO::printSuccess("> - successfully compiled SCSS");
    }

    /**
     * @throws Exception
     */
    protected function compileJavascript(): void {
        //compile javascript
        IO::printWarning("> compiling JavaScript");

        $jsHelper = new JavascriptHelper();
        EventEmitter::get()->publish(new JavascriptCompileEvent($jsHelper));
        $jsHelper->compileAll($this->parameters['namespace']);

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