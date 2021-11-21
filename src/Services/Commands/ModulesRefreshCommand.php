<?php

namespace spawnApp\Services\Commands;

use bin\spawn\IO;
use Doctrine\DBAL\Exception;
use spawn\system\Core\Base\Helper\DatabaseHelper;
use spawn\system\Core\Contents\Modules\Module;
use spawn\system\Core\Custom\AbstractCommand;
use spawn\system\Core\Helper\UUID;
use spawn\system\Core\Services\ServiceContainer;
use spawn\system\Core\Services\ServiceContainerProvider;
use spawn\system\Throwables\WrongEntityForRepositoryException;
use spawnApp\Database\ModuleTable\ModuleEntity;
use spawnApp\Database\ModuleTable\ModuleRepository;
use spawnApp\Models\ModuleStorage;
use spawnApp\Services\SeoUrlManager;

class ModulesRefreshCommand extends AbstractCommand {

    protected DatabaseHelper $databaseHelper;
    protected ModuleRepository $moduleRepository;

    public function __construct(
        DatabaseHelper $databaseHelper,
        ModuleRepository $moduleRepository
    )
    {
        $this->databaseHelper = $databaseHelper;
        $this->moduleRepository = $moduleRepository;
    }

    public static function getCommand(): string
    {
        return 'modules:refresh';
    }

    public static function getShortDescription(): string
    {
        return 'Refreshes module data';
    }

    protected static function getParameters(): array
    {
        return [
            'modules' => 'm',
            'actions' => 'a',
            'delete' => 'D'
        ];
    }

    /**
     * @param array $parameters
     * @return int
     * @throws Exception
     * @throws WrongEntityForRepositoryException
     */
    public function execute(array $parameters): int
    {
        $refreshAll = !($parameters['modules'] || $parameters['actions']);

        if($parameters['modules'] || $refreshAll) {
            $this->refreshModules(!!$parameters['delete']);
        }

        if($parameters['actions'] || $refreshAll) {
            $this->refreshActions(!!$parameters['delete']);
        }

        return 0;
    }

    /**
     * @param bool $deleteMissing
     * @throws Exception
     * @throws WrongEntityForRepositoryException
     */
    protected function refreshModules(bool $deleteMissing = false): void {
        IO::printWarning('> Refreshing Modules');

        $moduleCollection = ListModulesCommand::getModuleList(false);
        $existingModules = $this->moduleRepository->search();

        $registeredPaths = [];
        /** @var ModuleEntity $existingModule */
        foreach($existingModules->getArray() as $existingModule) {
            $registeredPaths[] = $existingModule->getPath();
        }

        $newModules = [];
        /** @var Module $availableModule */
        foreach($moduleCollection->getModuleList() as $availableModule) {
            if(!in_array($availableModule->getBasePath(), $registeredPaths)) {
                $newModules = $availableModule;
            }
        }

        /** @var Module $module */
        foreach($newModules as $module) {
            $resourceConfig = json_encode([
                "namespace" => $module->getResourceNamespace(),
                "namespace_raw" => $module->getResourceNamespaceRaw(),
                "using" => $module->getUsingNamespaces(),
                "path" => $module->getResourcePath(),
                "weight" => $module->getResourceWeight()
            ]);
            $informations = json_encode($module->getInformation());

            $moduleEntity = new ModuleEntity(
                $module->getSlug(),
                $module->getBasePath(),
                false,
                $informations,
                $resourceConfig
            );

            $this->moduleRepository->upsert($moduleEntity);
            IO::printWarning('Added new module: ' .$moduleEntity->getSlug());
        }

        if($deleteMissing) {
            //delete missing modules
            /** @var ModuleStorage $module */
            foreach($existingModules as $module) {
                if(!$module->isActive() && !file_exists(ROOT.$module->getPath())) {
                    $this->moduleRepository->delete(['id' => UUID::hexToBytes($module->getId())]);
                    IO::printWarning('Removed stale module: ' . $module->getSlug(), 1);
                    continue;
                }
            }
        }


        IO::printSuccess('> successfully refreshed modules');
    }

    /**
     * @param bool $removeStaleActions
     * @throws Exception
     * @throws WrongEntityForRepositoryException
     */
    protected function refreshActions(bool $removeStaleActions = false): void {
        /** @var ServiceContainer $serviceContainer */
        $serviceContainer = ServiceContainerProvider::getServiceContainer();
        /** @var SeoUrlManager $seoUrlManager */
        $seoUrlManager = $serviceContainer->getServiceInstance('system.service.seo_url_manager');

        IO::printLine('> Adding new available Methods and removing stale ones', IO::YELLOW_TEXT);

        $result = $seoUrlManager->refreshSeoUrlEntries($removeStaleActions);

        IO::printSuccess('> Added '.$result['added'].' Methods', 1);
        if(isset($result['removed'])) {
            IO::printSuccess('> Removed '.$result['removed'].' Methods', 1);
        }
    }

}
