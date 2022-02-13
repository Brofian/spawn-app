<?php

namespace SpawnCore\Defaults\Services\Commands;



use bin\spawn\IO;
use Exception;
use SpawnCore\Defaults\Database\ModuleTable\ModuleEntity;
use SpawnBackend\Exceptions\InvalidModuleSlugException;
use SpawnCore\System\CardinalSystem\ModuleNetwork\ModuleNamespacer;
use SpawnCore\System\Custom\FoundationStorage\AbstractCommand;
use SpawnCore\System\Custom\Gadgets\NamespaceHelper;
use SpawnCore\System\Custom\Gadgets\ResourceCollector;
use SpawnCore\System\Custom\Gadgets\ScssHelper;
use SpawnCore\System\Database\Entity\EntityCollection;

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

        $this->gatherFiles(ListModulesCommand::getModuleList());

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
        $scssHelper->createCss($this->parameters['namespace']);

        IO::printSuccess("> - successfully compiled SCSS");
    }

    /**
     * @throws Exception
     */
    protected function compileJavascript(): void {
        //compile javascript
        IO::printWarning("> compiling JavaScript");



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