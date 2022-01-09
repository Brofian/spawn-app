<?php

namespace spawnApp\Services;

use spawnApp\Database\ConfigurationTable\ConfigurationEntity;
use spawnApp\Database\ConfigurationTable\ConfigurationRepository;
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