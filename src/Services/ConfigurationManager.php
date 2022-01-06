<?php

namespace spawnApp\Services;

use bin\spawn\IO;
use Exception;
use spawnApp\Database\ConfigurationTable\ConfigurationEntity;
use spawnApp\Database\ConfigurationTable\ConfigurationRepository;
use spawnApp\Database\ModuleTable\ModuleEntity;
use spawnApp\Services\Commands\ListModulesCommand;
use spawnApp\Services\Exceptions\MissingRequiredConfigurationFieldException;
use spawnCore\Custom\Gadgets\UUID;
use spawnCore\Custom\Gadgets\XMLContentModel;
use spawnCore\Custom\Gadgets\XMLReader;
use spawnCore\Database\Criteria\Criteria;
use spawnCore\Database\Entity\EntityCollection;

class ConfigurationManager {

    protected ConfigurationRepository $configurationRepository;
    protected array $configurations = [];

    public function __construct(
        ConfigurationRepository $configurationRepository
    )
    {
        $this->configurationRepository = $configurationRepository;
        $this->configurations = $this->loadConfigurations();
    }

    /**
     * @param mixed $default
     * @return mixed
     */
    public function getConfiguration(string $name, $default = null) {
        if(isset($this->configurations[$name])) {
            return $this->configurations[$name];
        }
        return $default;
    }

    protected function loadConfigurations(): array {
        /** @var EntityCollection $configEntities */
        $configEntities = $this->configurationRepository->search(new Criteria());
        $configurations = [];
        /** @var ConfigurationEntity $configEntity */
        foreach($configEntities as $configEntity) {
            $configurations[$configEntity->getInternalName()] = $this->configurationEntityToValue($configEntity);
        }
        return $configurations;
    }

    /**
     * @return mixed
     */
    protected function configurationEntityToValue(ConfigurationEntity $entity) {
        switch ($entity->getType()) {
            case ConfigurationSystem::TYPE_NUMBER:
                return (double)$entity->getValue();
            case ConfigurationSystem::TYPE_BOOL:
                return (bool)$entity->getValue();
            default:
                // ConfigurationSystem::TYPE_TEXT,
                // ConfigurationSystem::TYPE_SELECT,
                // ConfigurationSystem::TYPE_ENTITY
                return $entity->getValue();
        }
    }




}