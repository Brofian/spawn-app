<?php

namespace spawnApp\Services;

use bin\spawn\IO;
use spawnApp\Database\LanguageTable\LanguageEntity;
use spawnApp\Database\LanguageTable\LanguageRepository;
use spawnApp\Database\ModuleTable\ModuleEntity;
use spawnApp\Database\SnippetTable\SnippetEntity;
use spawnApp\Database\SnippetTable\SnippetRepository;
use spawnApp\Services\Commands\ListModulesCommand;
use spawnApp\Services\Exceptions\AddedSnippetForMissingLanguageException;
use spawnCore\Custom\Gadgets\FileEditor;
use spawnCore\Custom\Gadgets\UUID;
use spawnCore\Database\Criteria\Criteria;
use spawnCore\Database\Criteria\Filters\EqualsFilter;
use spawnCore\Database\Criteria\Filters\LikeFilter;

class SnippetManager {

    protected SnippetRepository $snippetRepository;
    protected LanguageRepository $languageRepository;
    protected array $availableLanguages = [];
    protected array $languageFallbacks = [];

    protected array $loadedSnippets = [];

    public static string $language = 'EN';

    public function __construct(
        SnippetRepository $snippetRepository,
        LanguageRepository $languageRepository
    )
    {
        $this->snippetRepository = $snippetRepository;
        $this->languageRepository = $languageRepository;
        $this->loadAvailableLanguages();
    }

    public function getSnippet(string $path, string $language = 'EN', array $useForLanguages = []): string {
        if(!isset($this->availableLanguages[$language])) {
            return "Invalid Language\"$language\"!";
        }
        $languageKey = $this->availableLanguages[$language];

        //check if snippet is already loaded
        if(isset($this->loadedSnippets[$languageKey][$path])) {
            return $this->loadedSnippets[$languageKey][$path];
        }

        //the snippet needs to be loaded first
        $namespacePath = substr($path, 0, strrpos($path, '.'));
        $this->loadPath($namespacePath, $languageKey);


        //check again, if the snippet is now loaded
        if(isset($this->loadedSnippets[$languageKey][$path])) {
            $value = $this->loadedSnippets[$languageKey][$path];

            if(!empty($useForLanguages)) {
                foreach($useForLanguages as $forLanguage) {
                    $langKey = $this->availableLanguages[$forLanguage];
                    $this->loadedSnippets[$langKey][$path] = $value;
                }
            }

            return $value;
        }
        elseif(isset($this->languageFallbacks[$language])) {
            //snippet is not available in the database -> search in the fallback language
            $useForLanguages[] = $language;
            return $this->getSnippet($path, $this->languageFallbacks[$language], $useForLanguages);
        }

        //if nothing is found, return the searched string
        return $path;
    }

    protected function loadPath(string $path, string $languageId): void {
        $snippetEntities = $this->snippetRepository->search(
            new Criteria(
                new LikeFilter('path', "$path%"),
                new EqualsFilter('languageId', UUID::hexToBytes($languageId))
            )
        );

        /** @var SnippetEntity $snippetEntity */
        foreach($snippetEntities as $snippetEntity) {
            $langKey = $snippetEntity->getLanguageId();
            $path = $snippetEntity->getPath();
            $value = $snippetEntity->getValue();
            $this->loadedSnippets[$langKey][$path] = $value;
        }

    }

    protected function loadAvailableLanguages(): void {
        $languageEntities = $this->languageRepository->search(new Criteria());
        /** @var LanguageEntity $languageEntity */
        foreach($languageEntities as $languageEntity) {
            $this->availableLanguages[$languageEntity->getShort()] = $languageEntity->getId();
            if($languageEntity->getParentId()) {
                $this->languageFallbacks[$languageEntity->getShort()] = $languageEntity->getParentId();
            }
        }

        $languages = array_flip($this->availableLanguages);
        foreach($this->languageFallbacks as $short => &$languageFallback) {
            $languageFallback = $languages[$languageFallback];
        }
    }






}