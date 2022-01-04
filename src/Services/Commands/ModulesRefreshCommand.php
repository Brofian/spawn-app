<?php

namespace spawnApp\Services\Commands;

use bin\spawn\IO;
use Doctrine\DBAL\Exception;
use spawnApp\Database\ModuleTable\ModuleEntity;
use spawnApp\Database\ModuleTable\ModuleRepository;
use spawnApp\Services\ConfigurationManager;
use spawnApp\Services\SeoUrlManager;
use spawnApp\Services\SnippetManager;
use spawnCore\Custom\FoundationStorage\AbstractCommand;
use spawnCore\Custom\Gadgets\UUID;
use spawnCore\Custom\Throwables\DatabaseConnectionException;
use spawnCore\Custom\Throwables\WrongEntityForRepositoryException;
use spawnCore\Database\Criteria\Criteria;
use spawnCore\Database\Criteria\Filters\EqualsFilter;
use spawnCore\Database\Criteria\Filters\InvalidFilterValueException;
use spawnCore\Database\Entity\InvalidRepositoryInteractionException;
use spawnCore\Database\Entity\RepositoryException;
use spawnCore\Database\Helpers\DatabaseHelper;
use spawnCore\ServiceSystem\ServiceContainer;
use spawnCore\ServiceSystem\ServiceContainerProvider;

class ModulesRefreshCommand extends AbstractCommand {

    protected DatabaseHelper $databaseHelper;
    protected ModuleRepository $moduleRepository;
    protected SeoUrlManager $seoUrlManager;
    protected ConfigurationManager $configurationManager;
    protected SnippetManager $snippetManager;

    public function __construct(
        DatabaseHelper $databaseHelper,
        ModuleRepository $moduleRepository,
        SeoUrlManager $seoUrlManager,
        ConfigurationManager $configurationManager,
        SnippetManager $snippetManager
    )
    {
        $this->databaseHelper = $databaseHelper;
        $this->moduleRepository = $moduleRepository;
        $this->seoUrlManager = $seoUrlManager;
        $this->configurationManager = $configurationManager;
        $this->snippetManager = $snippetManager;
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
            'configurations' => 'c',
            'snippets' => 's',
            'delete' => 'delete'
        ];
    }

    /**
     * @param array $parameters
     * @return int
     * @throws Exception
     */
    public function execute(array $parameters): int
    {
        $refreshAll = !($parameters['modules'] || $parameters['actions'] || $parameters['configurations']);

        try {
            if($parameters['modules'] || $refreshAll) {
                $this->refreshModules(!!$parameters['delete']);
            }

            if($parameters['actions'] || $refreshAll) {
                $this->refreshActions(!!$parameters['delete']);
            }

            if($parameters['configurations'] || $refreshAll) {
                $this->refreshConfig(!!$parameters['delete']);
            }

            if($parameters['snippets'] || $refreshAll) {
                $this->refreshSnippets(!!$parameters['delete']);
            }
        } catch (
            Exception |
            WrongEntityForRepositoryException |
            DatabaseConnectionException |
            InvalidRepositoryInteractionException |
            InvalidFilterValueException |
            RepositoryException
            $e)
        {
            throw new Exception($e->getMessage(), $e->getCode(), $e);
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
     * @throws InvalidFilterValueException
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
                    $this->moduleRepository->delete(
                        new Criteria(
                            new EqualsFilter('id', UUID::hexToBytes($module->getId()))
                        )
                    );
                    IO::printWarning('Removed stale module: ' . $module->getSlug(), 1);
                    continue;
                }
            }
        }


        IO::printSuccess('> Successfully refreshed modules');
    }

    /**
     * @param bool $removeStaleActions
     * @throws DatabaseConnectionException
     * @throws Exception
     * @throws InvalidRepositoryInteractionException
     * @throws RepositoryException
     * @throws WrongEntityForRepositoryException
     */
    protected function refreshActions(bool $removeStaleActions = false): void {
        IO::printWarning('> Adding new available Methods' . ($removeStaleActions ? 'and removing stale ones' : ''));

        $result = $this->seoUrlManager->refreshSeoUrlEntries($removeStaleActions);

        IO::printSuccess('> Added '.$result['added'].' Methods', 1);
        if(isset($result['removed'])) {
            IO::printSuccess('> Removed '.$result['removed'].' Methods', 1);
        }
    }


    protected function refreshConfig(bool $removeStaleConfigurations = false): void {
        IO::printWarning('> Adding new available configurations' . ($removeStaleConfigurations ? ' and removing stale ones' : ''));

        $result = $this->configurationManager->updateConfigurationEntries($removeStaleConfigurations);


        IO::printSuccess('> Added '.$result['added'].' configurations', 1);
        IO::printSuccess('> Updated '.$result['updated'].' configurations', 1);
        if(isset($result['removed'])) {
            IO::printSuccess('> Removed '.$result['removed'].' configurations', 1);
        }
    }


    protected function refreshSnippets(bool $removeStaleConfigurations = false): void {
        IO::printWarning('> Adding new available snippets' . ($removeStaleConfigurations ? ' and removing stale ones' : ''));

        $result = $this->snippetManager->updateSnippetEntries($removeStaleConfigurations);


        IO::printSuccess('> Added '.$result['added'].' snippets', 1);
        if(isset($result['removed'])) {
            IO::printSuccess('> Removed '.$result['removed'].' snippets', 1);
        }
    }

}
