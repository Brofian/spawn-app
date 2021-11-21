<?php

namespace spawnApp\Services\Commands;

use bin\spawn\IO;
use spawn\system\Core\Base\Helper\DatabaseHelper;
use spawn\system\Core\Contents\Modules\Module;
use spawn\system\Core\Contents\Modules\ModuleCollection;
use spawn\system\Core\Contents\Modules\ModuleLoader;
use spawn\system\Core\Custom\AbstractCommand;

class ListModulesCommand extends AbstractCommand {

    public static function getCommand(): string
    {
        return 'modules:list';
    }

    public static function getShortDescription(): string
    {
        return 'Outputs a list of all available modules';
    }

    protected static function getParameters(): array
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
        $moduleList = self::getModuleList($refresh);
        $this->outputModuleCollectionAsTable($moduleList);

        return 0;
    }

    public static function getModuleList(bool $refresh = false): ModuleCollection {
        $dbHelper = new DatabaseHelper();
        $moduleLoader = new ModuleLoader();
        return $moduleLoader->readModules($dbHelper->getConnection(), $refresh);
    }

    protected function outputModuleCollectionAsTable(ModuleCollection $collection): void {
        $moduleList = [];
        $moduleList[] = [
            "ID",
            "Module",
            "Active",
            "Version",
            "Author"
        ];

        /** @var Module $module */
        foreach($collection->getModuleList() as $module) {
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