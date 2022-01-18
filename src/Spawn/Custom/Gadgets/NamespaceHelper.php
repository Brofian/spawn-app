<?php

namespace spawnCore\Custom\Gadgets;

use spawnApp\Database\ModuleTable\ModuleEntity;
use spawnCore\Database\Entity\EntityCollection;

class NamespaceHelper {

    public static function getNamespacesFromModuleCollection(EntityCollection $moduleCollection): array {
        //gather available namespaces
        list($slugToModule, $modulesInNamespaces) = self::gatherAvailableNamespaces($moduleCollection);

        //assign modules to namespaces
        self::assignModulesToNamespaceList($moduleCollection, $modulesInNamespaces, $slugToModule);

        return $modulesInNamespaces;
    }

    protected static function assignModulesToNamespaceList(EntityCollection $moduleCollection, array &$modulesInNamespaces, array $slugToModule): void {

        foreach($modulesInNamespaces as $namespace => &$slugs) {
            do {
                $hasChanged = false;
                $slugsToAdd = [];
                foreach($slugs as $slug) {
                    if(!isset($slugToModule[$slug])) {
                        continue;
                    }
                    $module = $slugToModule[$slug];
                    $using = $module->getResourceConfigValue('using');
                    if(is_array($using)) {
                        $slugsToAdd = array_merge($using);
                    }
                }

                foreach($slugsToAdd as $slugToAdd) {
                    if(!in_array($slugToAdd, $slugs)) {
                        $slugs[] = $slugToAdd;
                        $hasChanged = true;
                    }
                }
            }
            while($hasChanged);

            $slugs = array_unique($slugs);
        }
    }


    protected static function gatherAvailableNamespaces(EntityCollection $moduleCollection): array {
        $slugToModule = [];
        $modulesInNamespaces = [];

        /** @var ModuleEntity $module */
        foreach($moduleCollection->getArray() as $module) {
            $namespace = $module->getNamespace();
            $slug = $module->getSlug();
            $slugToModule[$slug] = &$module;

            if(!isset($modulesInNamespaces[$namespace])) {
                $modulesInNamespaces[$namespace] = [];
            }
            $modulesInNamespaces[$namespace][] = $slug;
        }

        return [
            $slugToModule,
            $modulesInNamespaces
        ];
    }


}