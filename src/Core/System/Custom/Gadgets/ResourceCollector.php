<?php declare(strict_types = 1);
namespace SpawnCore\System\Custom\Gadgets;


use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SpawnCore\Defaults\Database\ModuleTable\ModuleEntity;
use SpawnCore\System\CardinalSystem\ModuleNetwork\ModuleNamespacer;
use SpawnCore\System\Database\Entity\EntityCollection;
use SplFileInfo;

class ResourceCollector
{

    public const PUBLIC_ASSET_PATH = ROOT . '/public/cache';
    public const RESOURCE_CACHE_PATH = ROOT . '/var/cache/resources/modules';


    /**
     * @return bool
     */
    public static function isGatheringNeeded(): bool
    {
        return true;
    }


    public function gatherModuleData(EntityCollection $moduleCollection): void
    {
        /** @var ModuleEntity $module */
        foreach ($moduleCollection->getArray() as $module) {
            //move the modules from this namespace
            $this->moveModuleData($module);
        }

        $namespaces = NamespaceHelper::getNamespacesFromModuleCollection($moduleCollection);

        foreach($namespaces as $namespace => $moduleSlugs) {
            //create scss file
            $scssIndexFile = '';
            //create js file
            $jsIndexFile = '';

            foreach($moduleSlugs as $slug) {
                if(file_exists(self::RESOURCE_CACHE_PATH . '/scss/'.$slug.'/base.scss')) {
                    $scssIndexFile .= '@import "'.$slug .'/base.scss";'. PHP_EOL;
                }
                if(file_exists(self::RESOURCE_CACHE_PATH . '/js/'.$slug.'/main.js')) {
                    $jsIndexFile .= 'import "./'.$slug.'/main.js";' . PHP_EOL;
                }
            }

            //create entry file for css and js compilation
            FileEditor::createFile(
                self::RESOURCE_CACHE_PATH . '/scss/'.$namespace.'_index.scss',
                "/* Index File - generated automatically*/" . PHP_EOL . PHP_EOL . $scssIndexFile
            );
            FileEditor::createFile(
                self::RESOURCE_CACHE_PATH . '/js/'.$namespace.'_index.js',
                "/* Index File - generated automatically*/" . PHP_EOL . PHP_EOL . $jsIndexFile
            );

        }
    }


    private function moveModuleData(ModuleEntity $module): void
    {
        $resourcePath = $module->getResourceConfigValue('path');

        if (!$resourcePath) {
            return;
        }

        $absoluteModuleResourcePath = ROOT . $module->getPath() . $resourcePath;

        /*
         * SCSS
         */
        $scssFolder = $absoluteModuleResourcePath . '/public/scss';
        self::copyFolderRecursive($scssFolder, self::RESOURCE_CACHE_PATH . '/scss/' . $module->getSlug());


        /*
         * Javascript
         */
        $jsFolder = $absoluteModuleResourcePath . '/public/js';
        self::copyFolderRecursive($jsFolder, self::RESOURCE_CACHE_PATH . '/js/' . $module->getSlug());

        /*
         * Assets
         */
        $assetsFolder = $absoluteModuleResourcePath . '/public/assets';
        $assetsTargetFolder = self::PUBLIC_ASSET_PATH . '/' . ModuleNamespacer::hashNamespace($module->getNamespace()) . '/assets/';
        self::copyFolderRecursive($assetsFolder, $assetsTargetFolder);
    }



    public static function copyFolderRecursive(string $source, string $dest): void
    {

        if (!file_exists($source)) {
            return;
        }


        FileEditor::createFolder($dest);

        /**
         * @var RecursiveIteratorIterator $iterator
         * @var SplFileInfo $item
         */
        foreach (
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::SELF_FIRST)
            as $item
        ) {
            if ($item->isDir()) {
                if (!file_exists($dest . '/' . $iterator->getSubPathName())) {
                    FileEditor::createFolder($dest . '/' . $iterator->getSubPathName());
                }
            } else {
                copy($item->getPathname(), $dest . '/' . $iterator->getSubPathName());
            }
        }

    }


}