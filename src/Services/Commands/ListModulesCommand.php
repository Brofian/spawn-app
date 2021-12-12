<?php

namespace spawnApp\Services\Commands;

use bin\spawn\IO;
use spawnCore\Database\Entity\EntityCollection;
use spawn\system\Core\Base\Helper\DatabaseHelper;
use spawn\system\Core\Contents\Modules\ModuleLoader;
use spawn\system\Core\Custom\AbstractCommand;
use spawnApp\Database\ModuleTable\ModuleEntity;

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
        $dbHelper = new DatabaseHelper();
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