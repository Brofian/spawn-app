<?php

namespace spawnApp\Services\Commands;

use bin\spawn\IO;
use Doctrine\DBAL\Exception;
use spawnApp\Database\ModuleTable\ModuleEntity;
use spawnApp\Database\ModuleTable\ModuleRepository;
use spawnApp\Services\SeoUrlManager;
use spawnCore\Custom\FoundationStorage\AbstractCommand;
use spawnCore\Custom\Gadgets\UUID;
use spawnCore\Custom\Throwables\DatabaseConnectionException;
use spawnCore\Custom\Throwables\WrongEntityForRepositoryException;
use spawnCore\Database\Criteria\Criteria;
use spawnCore\Database\Entity\InvalidRepositoryInteractionException;
use spawnCore\Database\Entity\RepositoryException;
use spawnCore\Database\Helpers\DatabaseHelper;
use spawnCore\ServiceSystem\ServiceContainer;
use spawnCore\ServiceSystem\ServiceContainerProvider;

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

    public static function getParameters(): array
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
     * @throws DatabaseConnectionException
     * @throws InvalidRepositoryInteractionException
     * @throws RepositoryException
     */
    protected function refreshModules(bool $deleteMissing = false): void {
        IO::printWarning('> Refreshing Modules');

        $moduleCollection = ListModulesCommand::getModuleList(true);
        $existingModules = $this->moduleRepository->search(new Criteria());

        $registeredPaths = [];
        /** @var ModuleEntity $existingModule */
        foreach($existingModules->getArray() as $existingModule) {
            $registeredPaths[] = $existingModule->getPath();
        }

        $newModules = [];
        /** @var ModuleEntity $availableModule */
        foreach($moduleCollection->getArray() as $availableModule) {
            if(!in_array($availableModule->getPath(), $registeredPaths)) {
                $newModules[] = $availableModule;
            }
        }

        /** @var ModuleEntity $module */
        foreach($newModules as $module) {
            $this->moduleRepository->upsert($module);
            IO::printWarning('   :: Added new module: ' .$module->getSlug());
        }

        if($deleteMissing) {
            //delete missing modules
            /** @var ModuleEntity $module */
            foreach($existingModules as $module) {
                if(!$module->isActive() && !file_exists(ROOT.$module->getPath())) {
                    $this->moduleRepository->delete(['id' => UUID::hexToBytes($module->getId())]);
                    IO::printWarning('Removed stale module: ' . $module->getSlug(), 1);
                    continue;
                }
            }
        }


        IO::printSuccess('> Successfully refreshed modules');
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
