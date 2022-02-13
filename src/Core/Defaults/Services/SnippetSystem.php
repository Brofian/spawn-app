<?php

namespace SpawnCore\Defaults\Services;

use bin\spawn\IO;
use Doctrine\DBAL\Exception;
use SpawnCore\Defaults\Database\LanguageTable\LanguageEntity;
use SpawnCore\Defaults\Database\LanguageTable\LanguageRepository;
use SpawnCore\Defaults\Database\ModuleTable\ModuleEntity;
use SpawnCore\Defaults\Database\SnippetTable\SnippetEntity;
use SpawnCore\Defaults\Database\SnippetTable\SnippetRepository;
use SpawnCore\Defaults\Services\Commands\ListModulesCommand;
use SpawnCore\Defaults\Exceptions\AddedSnippetForMissingLanguageException;
use SpawnCore\System\Custom\Gadgets\FileEditor;
use SpawnCore\System\Custom\Throwables\DatabaseConnectionException;
use SpawnCore\System\Custom\Throwables\WrongEntityForRepositoryException;
use SpawnCore\System\Database\Criteria\Criteria;
use SpawnCore\System\Database\Entity\RepositoryException;

class SnippetSystem {

    public const SNIPPET_FILE_REGEX = '/^.*\.json$/m';

    protected SnippetRepository $snippetRepository;
    protected LanguageRepository $languageRepository;
    protected ?array $availableLanguages = null;


    public function __construct(
        SnippetRepository $snippetRepository,
        LanguageRepository $languageRepository
    )
    {
        $this->snippetRepository = $snippetRepository;
        $this->languageRepository = $languageRepository;
    }

    /**
     * @throws DatabaseConnectionException
     * @throws RepositoryException
     */
    protected function loadAvailableLanguages(): void {
        if(is_array($this->availableLanguages)) {
            return;
        }

        $languageEntities = $this->languageRepository->search(new Criteria());
        /** @var LanguageEntity $languageEntity */
        foreach($languageEntities as $languageEntity) {
            $this->availableLanguages[$languageEntity->getShort()] = $languageEntity->getId();
        }
    }


    /**
     * @throws AddedSnippetForMissingLanguageException
     * @throws DatabaseConnectionException
     * @throws RepositoryException
     * @throws Exception
     * @throws WrongEntityForRepositoryException
     */
    public function updateSnippetEntries(): array {
        $this->loadAvailableLanguages();
        $result = [
            'added' => 0
        ];

        $snippetsFromFiles = $this->loadSnippetsFromModules();
        $snippetsFromDatabase = $this->loadSnippetsFromDatabase();

        $addedSnippetsTotal = 0;
        foreach($snippetsFromFiles as $languageKey => $snippetList) {
            if(!isset($this->availableLanguages[$languageKey])) {
                throw new AddedSnippetForMissingLanguageException($languageKey);
            }
            $languageId = $this->availableLanguages[$languageKey];

            $addedSnippetsLanguage = 0;
            foreach($snippetList as $path => $snippet) {
                //only add, if this snippet does not exist yet
                if(isset($snippetsFromDatabase[$languageId][$path])) {
                    continue;
                }

                $snippetEntity = new SnippetEntity($path, $snippet, $languageId);
                $this->snippetRepository->upsert($snippetEntity);
                $addedSnippetsLanguage++;
            }

            IO::printSuccess('Added '.$addedSnippetsLanguage.' snippets to language ' . $languageKey, 1);
            $addedSnippetsTotal += $addedSnippetsLanguage;
        }

        IO::printSuccess('Added '.$addedSnippetsTotal.' snippets in total');

        return $result;
    }

    /**
     * @throws DatabaseConnectionException
     * @throws RepositoryException
     */
    public function loadSnippetsFromDatabase(): array {
        $this->loadAvailableLanguages();
        $snippetEntities = $this->snippetRepository->search(new Criteria());
        $snippets = [];
        /** @var SnippetEntity $snippetEntity */
        foreach($snippetEntities as $snippetEntity) {
            $snippets[$snippetEntity->getLanguageId()][$snippetEntity->getPath()] = $snippetEntity->getValue();
        }

        return $snippets;
    }

    /**
     * @throws DatabaseConnectionException
     * @throws RepositoryException
     */
    public function loadSnippetsFromModules(): array {
        $this->loadAvailableLanguages();
        $snippetArraysFromFiles = [];

        /** @var ModuleEntity $module */
        foreach(ListModulesCommand::getModuleList(true)->getArray() as $module) {
            IO::printWarning(IO::TAB . ':: Now loading snippet files from ' . $module->getSlug(), 2);
            $modulePath = $module->getPath();
            $moduleResourcePath = $module->getResourceConfigValue('path');
            $moduleSnippetFolder = ROOT . $modulePath . $moduleResourcePath . '/snippets';
            if(!is_dir($moduleSnippetFolder)) {
                continue;
            }

            foreach(scandir($moduleSnippetFolder) as $itemInFolder) {
                if(preg_match(self::SNIPPET_FILE_REGEX, $itemInFolder)) {
                    $json = FileEditor::getFileContent($moduleSnippetFolder . '/' . $itemInFolder);
                    $snippetsInFile = $this->interpretSnippetJson($json);

                    $snippetArraysFromFiles[] = $snippetsInFile;
                }
            }
        }

        return array_merge_recursive(...$snippetArraysFromFiles);
    }


    protected function interpretSnippetJson(string $json): array {
        $data = json_decode($json, true);

        $snippets = [];
        foreach($data as $language => $arrays) {
            $values = [];
            $this->gatherValuesRecursively($arrays, $values);

            if(!isset($snippets[$language])) {
                $snippets[$language] = [];
            }
            $snippets[$language] = array_merge($snippets[$language], $values);
        }

        return $snippets;
    }

    protected function gatherValuesRecursively(array $arr, array &$values, string $path = ''): void {
        foreach($arr as $key => $element) {
            $fullPath = "$path.$key";
            if(is_array($element)) {
                $this->gatherValuesRecursively($element, $values, $fullPath);
            }
            else {
                $values[ltrim($fullPath, '.')] = $element;
            }
        }
    }


}