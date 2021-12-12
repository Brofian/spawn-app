<?php

namespace spawnApp\Services\Commands;


use bin\spawn\IO;
use spawnApp\Database\ModuleTable\ModuleEntity;
use spawnCore\CardinalSystem\ModuleNetwork\ModuleLoader;
use spawnCore\Custom\FoundationStorage\AbstractCommand;
use spawnCore\Database\Entity\EntityCollection;
use spawnCore\Database\Helpers\DatabaseHelper;

class ListModulesCommand extends AbstractCommand {

    public static function getCommand(): string
    {
        return 'modules:list';
    }

    public static function getShortDescription(): string
    {
        return 'Outputs a list of all available modules';
    }

    public static function getParameters(): array
    {
        return [
            'refresh' => ['r', '--refresh']
        ];
    }

    /**
     * @inheritDoc
     */
    public function execute(array $parameters): int
    {
        $refresh = !!$parameters['refresh'];
        $moduleList = self::getModuleList();
        $this->outputModuleCollectionAsTable($moduleList);

        return 0;
    }

    public static function getModuleList(): EntityCollection {
        $moduleLoader = new ModuleLoader();
        return $moduleLoader->loadModules();
    }

    protected function outputModuleCollectionAsTable(EntityCollection $collection): void {
        $moduleList = [];
        $moduleList[] = [
            "ID",
            "Module",
            "Active",
            "Version",
            "Author"
        ];

        /** @var ModuleEntity $module */
        foreach($collection->getArray() as $module) {
            $moduleList[] = [
                $module->getId(),
                $module->getSlug(),
                $module->isActive() ? "Yes" : "No",
                $module->getInformation("version"),
                $module->getInformation("author")
            ];
        }

        IO::printAsTable($moduleList, true);
    }

}