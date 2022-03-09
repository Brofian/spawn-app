<?php

namespace SpawnCore\Defaults\Commands;


use bin\spawn\IO;
use Doctrine\DBAL\Exception;
use SpawnCore\Defaults\Database\ModuleTable\ModuleEntity;
use SpawnCore\System\CardinalSystem\ModuleNetwork\ModuleLoader;
use SpawnCore\System\Custom\FoundationStorage\AbstractCommand;
use SpawnCore\System\Custom\Throwables\DatabaseConnectionException;
use SpawnCore\System\Database\Entity\EntityCollection;
use SpawnCore\System\Database\Entity\RepositoryException;

class ListModulesCommand extends AbstractCommand {

    public static ?EntityCollection $moduleList = null;

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
     * @throws DatabaseConnectionException
     * @throws Exception
     * @throws RepositoryException
     */
    public function execute(array $parameters): int
    {
        $refresh = (bool)$parameters['refresh'];
        $moduleList = self::getModuleList($refresh);
        $this->outputModuleCollectionAsTable($moduleList);

        return 0;
    }

    /**
     * @throws Exception
     * @throws DatabaseConnectionException
     * @throws RepositoryException
     */
    public static function getModuleList(bool $forceReloadFromFiles = false): EntityCollection {
        if(!self::$moduleList || $forceReloadFromFiles) {
            self::$moduleList = (new ModuleLoader())->loadModules($forceReloadFromFiles);
        }

        return self::$moduleList;
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