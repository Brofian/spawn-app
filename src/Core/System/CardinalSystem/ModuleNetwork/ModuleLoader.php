<?php declare(strict_types=1);

namespace SpawnCore\System\CardinalSystem\ModuleNetwork;

use Doctrine\DBAL\Exception;
use SpawnCore\Defaults\Database\ModuleTable\ModuleEntity;
use SpawnCore\Defaults\Database\ModuleTable\ModuleRepository;
use SpawnCore\Defaults\Database\ModuleTable\ModuleTable;
use SpawnCore\System\Custom\Gadgets\Slugifier;
use SpawnCore\System\Custom\Gadgets\XMLContentModel;
use SpawnCore\System\Custom\Gadgets\XMLReader;
use SpawnCore\System\Custom\Throwables\DatabaseConnectionException;
use SpawnCore\System\Database\Criteria\Criteria;
use SpawnCore\System\Database\Entity\EntityCollection;
use SpawnCore\System\Database\Entity\RepositoryException;
use SpawnCore\System\Database\Helpers\DatabaseHelper;

class ModuleLoader
{

    const REL_XML_PATH = "/plugin.xml";

    protected array $moduleRootPaths = [
        ROOT . "/src/custom",
        ROOT . "/vendor"
    ];

    protected array $ignoredDirs = [
        '.',
        '..',
    ];

    /**
     * @throws DatabaseConnectionException
     * @throws RepositoryException
     * @throws Exception
     */
    public function loadModules(bool $forceReloadFromFiles = false): EntityCollection
    {
        if(!$forceReloadFromFiles) {
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
            foreach($moduleResources->getChildren() as $configChild) {
                $value = $configChild->getValue();

                // special cases
                if($configChild->getType() == 'namespace') {
                    $value = $value ?? ModuleNamespacer::FALLBACK_NAMESPACE;
                }
                elseif($configChild->getType() == 'using') {
                    $useNamespaces = [];
                    foreach($configChild->getChildren() as $child) {
                        $useNamespaces[] = $child->getValue();
                    }

                    $value = $useNamespaces;
                }

                $config[$configChild->getType()] = $value;
            }
        }


        return $data;
    }

}