<?php

namespace SpawnCore\Defaults\Commands;

use bin\spawn\IO;
use Doctrine\DBAL\Exception;
use SpawnCore\Defaults\Database\ModuleTable\ModuleEntity;
use SpawnCore\Defaults\Exceptions\AddedSnippetForMissingLanguageException;
use SpawnCore\Defaults\Services\ConfigurationSystem;
use SpawnCore\Defaults\Services\SeoUrlManager;
use SpawnCore\Defaults\Services\SnippetSystem;
use SpawnCore\System\Custom\FoundationStorage\AbstractCommand;
use SpawnCore\System\Custom\Gadgets\UUID;
use SpawnCore\System\Custom\Throwables\DatabaseConnectionException;
use SpawnCore\System\Custom\Throwables\WrongEntityForRepositoryException;
use SpawnCore\System\Database\Criteria\Criteria;
use SpawnCore\System\Database\Criteria\Filters\EqualsFilter;
use SpawnCore\System\Database\Entity\InvalidRepositoryInteractionException;
use SpawnCore\System\Database\Entity\RepositoryException;
use SpawnCore\System\Database\Entity\TableRepository;
use SpawnCore\System\Database\Helpers\DatabaseHelper;

class ModulesRefreshCommand extends AbstractCommand {

    protected DatabaseHelper $databaseHelper;
    protected TableRepository $moduleRepository;
    protected SeoUrlManager $seoUrlManager;
    protected ConfigurationSystem $configurationSystem;
    protected SnippetSystem $snippetSystem;

    public function __construct(
        DatabaseHelper $databaseHelper,
        TableRepository $moduleRepository,
        SeoUrlManager $seoUrlManager,
        ConfigurationSystem $configurationSystem,
        SnippetSystem $snippetSystem
    )
    {
        $this->databaseHelper = $databaseHelper;
        $this->moduleRepository = $moduleRepository;
        $this->seoUrlManager = $seoUrlManager;
        $this->configurationSystem = $configurationSystem;
        $this->snippetSystem = $snippetSystem;
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
     * @throws Exception
     * @throws AddedSnippetForMissingLanguageException
     */
    public function execute(array $parameters): int
    {
        $refreshAll = !($parameters['modules'] || $parameters['actions'] || $parameters['configurations']);

/*        try {*/
            if($parameters['modules'] || $refreshAll) {
                $this->refreshModules((bool)$parameters['delete']);
            }

            if($parameters['actions'] || $refreshAll) {
                $this->refreshActions((bool)$parameters['delete']);
            }

            if($parameters['configurations'] || $refreshAll) {
                $this->refreshConfig((bool)$parameters['delete']);
            }

            if($parameters['snippets'] || $refreshAll) {
                $this->refreshSnippets();
            }
/*        } catch (
            Exception |
            WrongEntityForRepositoryException |
            DatabaseConnectionException |
            InvalidRepositoryInteractionException |
            RepositoryException
            $e)
        {
            throw new Exception($e->getMessage(), $e->getCode(), $e);
        }*/

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
            if(!in_array($availableModule->getPath(), $registeredPaths, true)) {
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
        IO::printSuccess('> Updated '.$result['updated'].' Methods', 1);
        if(isset($result['removed'])) {
            IO::printSuccess('> Removed '.$result['removed'].' Methods', 1);
        }
    }


    /**
     * @throws DatabaseConnectionException
     * @throws Exception
     * @throws InvalidRepositoryInteractionException
     * @throws RepositoryException
     * @throws WrongEntityForRepositoryException
     */
    protected function refreshConfig(bool $removeStaleConfigurations = false): void {
        IO::printWarning('> Adding new available configurations' . ($removeStaleConfigurations ? ' and removing stale ones' : ''));

        $result = $this->configurationSystem->updateConfigurationEntries($removeStaleConfigurations);


        IO::printSuccess('> Added '.$result['added'].' configurations', 1);
        IO::printSuccess('> Updated '.$result['updated'].' configurations', 1);
        if(isset($result['removed'])) {
            IO::printSuccess('> Removed '.$result['removed'].' configurations', 1);
        }
    }


    /**
     * @throws DatabaseConnectionException
     * @throws Exception
     * @throws RepositoryException
     * @throws WrongEntityForRepositoryException
     * @throws AddedSnippetForMissingLanguageException
     */
    protected function refreshSnippets(): void {
        IO::printWarning('> Adding new available snippets');

        $result = $this->snippetSystem->updateSnippetEntries();

        IO::printSuccess('> Added '.$result['added'].' snippets', 1);
    }

}
