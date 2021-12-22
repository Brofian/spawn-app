<?php declare(strict_types=1);

namespace spawnCore\CardinalSystem\ModuleNetwork;

use spawnApp\Database\ModuleTable\ModuleEntity;
use spawnApp\Database\ModuleTable\ModuleRepository;
use spawnApp\Database\ModuleTable\ModuleTable;
use spawnCore\Custom\Gadgets\Slugifier;
use spawnCore\Custom\Gadgets\XMLContentModel;
use spawnCore\Custom\Gadgets\XMLReader;
use spawnCore\Custom\Throwables\DatabaseConnectionException;
use spawnCore\Database\Criteria\Criteria;
use spawnCore\Database\Entity\EntityCollection;
use spawnCore\Database\Entity\RepositoryException;
use spawnCore\Database\Helpers\DatabaseHelper;

class ModuleLoader
{

    const REL_XML_PATH = "/plugin.xml";

    protected array $moduleRootPaths = [
        ROOT . "/custom",
        ROOT . "/vendor"
    ];

    protected array $ignoredDirs = [
        '.',
        '..',
    ];


    public function loadModules(): EntityCollection
    {
        $databaseTable = new DatabaseHelper();

        if ($this->doesCacheExist()) {
            return $this->readModulesFromCache();
        }

        if ($databaseTable->doesTableExist(ModuleTable::TABLE_NAME)) {
            $moduleCollection = $this->readModulesFromDB();
            if ($moduleCollection->count()) {
                return $moduleCollection;
            }
        }

        return $this->readModulesFromFileSystem();
    }

    protected function doesCacheExist(): bool
    {
        //TODO: There is currently no module file cache
        //      if there will be one added, also implement the readModulesFromCache function below
        return false;
    }

    protected function readModulesFromCache(): EntityCollection
    {
        $moduleCollection = new EntityCollection(ModuleEntity::class);

        //TODO: There is currently no module file cache
        //      if there will be one added, also implement the doesCacheExist function above

        return $moduleCollection;
    }

    /**
     * @return EntityCollection
     * @throws DatabaseConnectionException
     * @throws RepositoryException
     */
    protected function readModulesFromDB(): EntityCollection
    {
        //load modules from database
        $moduleRepository = new ModuleRepository(new ModuleTable());
        return $moduleRepository->search(new Criteria());
    }

    protected function readModulesFromFileSystem(): EntityCollection
    {
        $moduleCollection = new EntityCollection(ModuleEntity::class);

        foreach ($this->moduleRootPaths as $rootPath) {
            if (!is_dir($rootPath)) continue;
            $namespaces = scandir($rootPath);

            foreach ($namespaces as $namespace) {
                $namespacePath = "$rootPath/$namespace";
                if (in_array($namespace, $this->ignoredDirs) || !is_dir($namespacePath)) {
                    continue;
                }


                $possibleModulesForNamespace = scandir($namespacePath);

                foreach ($possibleModulesForNamespace as $possibleModule) {
                    $currentModulePath = "$namespacePath/$possibleModule";
                    if (in_array($possibleModule, $this->ignoredDirs) || !is_dir($currentModulePath)) {
                        continue;
                    }

                    if ($this->isModuleDirectory($currentModulePath)) {
                        $moduleEntity = $this->generateModuleEntityFromFolder(
                            $currentModulePath,
                            $slug = Slugifier::slugify($namespace . '-' . $possibleModule)
                        );
                        $moduleCollection->add($moduleEntity);
                    }
                }
            }
        }

        return $moduleCollection;
    }

    protected function isModuleDirectory(string $directory): bool
    {
        $xmlFilePath = "$directory/plugin.xml";
        return (file_exists($xmlFilePath) && is_file($xmlFilePath));
    }

    protected function generateModuleEntityFromFolder(string $absolutePath, string $slug): ModuleEntity
    {
        $moduleData = [
            'slug' => $slug,
            'path' => str_replace(ROOT, '', $absolutePath),
            'active' => Slugifier::isSystemSlug($slug),
            'id' => null
        ];

        $data = $this->getModuleDataFromXML($absolutePath . self::REL_XML_PATH);
        $moduleData['information'] = json_encode($data['information']);
        $moduleData['resourceConfig'] = json_encode($data['config']);

        return ModuleEntity::getEntityFromArray($moduleData);
    }

    protected function getModuleDataFromXML(string $xmlPath): array
    {
        $data = [
            'information' => [],
            'config' => []
        ];

        /** @var XMLContentModel $moduleXML */
        $moduleXML = (new XMLReader())->readFile($xmlPath);

        /*
         * Get module information
         */
        $moduleInfo = $moduleXML->getChildrenByType("info")->first();
        if ($moduleInfo) {
            /** @var XMLContentModel $childInfo */
            foreach ($moduleInfo->getChildren() as $childInfo) {
                $data['information'][$childInfo->getType()] = trim($childInfo->getValue());
            }
        }


        /*
         * Get module config
         */
        $config = &$data['config'];
        /** @var XMLContentModel $moduleResources */
        $moduleResources = $moduleXML->getChildrenByType("resources")->first();
        if ($moduleResources) {
            $config['path'] = $moduleResources->getValue();
            $config['weight'] = (int)$moduleResources->getAttribute("weight");
            $config['namespace'] = $moduleResources->getAttribute("namespace") ?? ModuleNamespacer::GLOBAL_NAMESPACE_RAW;
        }

        /*
         * Get module "use" data (to include resources from another namespace)
         */
        /** @var XMLContentModel $moduleUsing */
        $moduleUsing = $moduleXML->getChildrenByType("using")->first();
        if ($moduleUsing) {
            $usingNamespaces = [];
            foreach ($moduleUsing->getChildrenByType("namespace") as $namespace) {
                $usingNamespaces[] = $namespace->getValue();
            }
            $config['using'] = $usingNamespaces;
        }


        return $data;
    }

}