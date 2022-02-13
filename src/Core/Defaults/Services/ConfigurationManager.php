<?php

namespace SpawnCore\Defaults\Services;

use SpawnCore\Defaults\Database\ConfigurationTable\ConfigurationEntity;
use SpawnCore\Defaults\Database\ConfigurationTable\ConfigurationRepository;
use SpawnCore\System\Custom\Throwables\DatabaseConnectionException;
use SpawnCore\System\Database\Criteria\Criteria;
use SpawnCore\System\Database\Entity\EntityCollection;
use SpawnCore\System\Database\Entity\RepositoryException;

class ConfigurationManager {

    protected ConfigurationRepository $configurationRepository;
    protected array $configurations = [];

    /**
     * @throws DatabaseConnectionException
     * @throws RepositoryException
     */
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

    /**
     * @return array
     * @throws DatabaseConnectionException
     * @throws RepositoryException
     */
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