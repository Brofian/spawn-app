<?php

namespace spawnApp\Services;

use spawnApp\Database\LanguageTable\LanguageEntity;
use spawnApp\Database\LanguageTable\LanguageRepository;
use spawnApp\Database\ModuleTable\ModuleEntity;
use spawnApp\Database\SnippetTable\SnippetRepository;
use spawnApp\Services\Commands\ListModulesCommand;
use spawnCore\Custom\Gadgets\FileEditor;
use spawnCore\Database\Criteria\Criteria;

class SnippetManager {

    public const SNIPPET_FILE_REGEX = '/^.*\.json$/m';

    protected SnippetRepository $snippetRepository;
    protected LanguageRepository $languageRepository;
    protected array $availableLanguages = [];


    public function __construct(
        SnippetRepository $snippetRepository,
        LanguageRepository $languageRepository
    )
    {
        $this->snippetRepository = $snippetRepository;
        $this->languageRepository = $languageRepository;
        $this->loadAvailableLanguages();
    }

    protected function loadAvailableLanguages(): void {
        $languageEntities = $this->languageRepository->search(new Criteria());
        /** @var LanguageEntity $languageEntity */
        foreach($languageEntities as $languageEntity) {
            $this->availableLanguages[$languageEntity->getShort()] = $languageEntity->getShort();
        }
    }


    public function updateSnippetEntries(bool $removeStaleOnes = false): array {
        $result = [
            'added' => 0
        ];


        $snippetsFromFiles = $this->loadSnippetsFromModules();

        // TODO load snippets from db
        // compare fileSnippets and dbSnippets and add new ones, ignore existing ones

        return $result;
    }

    public function loadSnippetsFromModules(): array {

        /** @var ModuleEntity $module */
        foreach(ListModulesCommand::getModuleList(true)->getArray() as $module) {
            $modulePath = $module->getPath();
            $moduleResourcePath = $module->getResourceConfigValue('path');
            $moduleSnippetFolder = ROOT . $modulePath . $moduleResourcePath . '/snippets';
            if(!is_dir($moduleSnippetFolder)) {
                continue;
            }

            foreach(scandir($moduleSnippetFolder) as $itemInFolder) {
                if(preg_match(self::SNIPPET_FILE_REGEX, $itemInFolder)) {
                    $json = FileEditor::getFileContent($moduleSnippetFolder . '/' . $itemInFolder);
                    $data = $this->interpretSnippetJson($json);

                    dd(__FILE__ .':'. __LINE__, $data);

                    // TODO do something with this data
                    // merge all the found data into one array. The first level of the array should be the list of languages
                }
            }
        }

        return [];
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
            $path .= ".$key";
            if(is_array($element)) {
                $this->gatherValuesRecursively($element, $values, $path);
            }
            else {
                $values[ltrim($path, '.')] = $element;
            }
        }
    }


}