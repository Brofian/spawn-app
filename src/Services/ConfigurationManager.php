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
use spawnCore\Database\Criteria\Filters\InFilter;

class ConfigurationManager {

    public const TRUE_VALUE = 'true';

    protected ConfigurationRepository $configurationRepository;

    public function __construct(
        ConfigurationRepository $configurationRepository
    )
    {
        $this->configurationRepository = $configurationRepository;
    }


    public function updateConfigurationEntries(bool $removeStaleOnes = false): array {
        $result = [
            'added' => 0,
            'updated' => 0
        ];

        $configurations = $this->loadConfigurationFiles();
        $existingConfigurations = $this->loadExistingConfigurations();

        $result['added'] = $this->addConfigurations($configurations, $existingConfigurations);
        $result['updated'] = $this->updateConfigurations($configurations, $existingConfigurations);
        if($removeStaleOnes) {
            $result['removed'] = $this->removeConfigurations($configurations, $existingConfigurations);
        }


        return $result;
    }


    protected function removeConfigurations(array $configurations, array $existingConfigurations): int {
        $configurationsToRemove = [];

        //filter configurations for stale ones
        /** @var ConfigurationEntity $configurationEntity */
        foreach($existingConfigurations as $internalName => $configurationEntity) {
            if(!isset($configurations[$internalName])) {
                $configurationsToRemove[] = UUID::hexToBytes($configurationEntity->getId());
            }
        }

        if(!empty($configurationsToRemove)) {
            $criteria = new Criteria(
                new InFilter('id', $configurationsToRemove)
            );
            $this->configurationRepository->delete($criteria);
        }

        return count($configurationsToRemove);
    }

    protected function updateConfigurations(array $configurations, array $existingConfigurations): int {
        $configurationsToUpdate = [];

        //filter configurations for new ones
        foreach($configurations as $internalName => $configuration) {
            if(isset($existingConfigurations[$internalName])) {
                /** @var ConfigurationEntity $existing */
                $existing = $existingConfigurations[$internalName];

                //compare
                $isEqual = (
                    $configuration['type'] == $existing->getType() &&
                    $configuration['folder'] == $existing->getFolder() &&
                    compareArraysRecursive($configuration['definition'], $existing->getDefinition(true))
                );
                if(!$isEqual) {
                    $existing->setType($configuration['type']);
                    $existing->setFolder($configuration['folder']);
                    $existing->setDefinition($configuration['definition']);
                    $configurationsToUpdate[$internalName] = $existing;
                }
            }
        }

        /** @var ConfigurationEntity $configurationToUpdate */
        foreach($configurationsToUpdate as $configurationToUpdate) {
            $this->configurationRepository->upsert($configurationToUpdate);
        }

        return count($configurationsToUpdate);
    }


    protected function addConfigurations(array $configurations, array $existingConfigurations): int {

        $configurationsToAdd = [];
        //filter configurations for new ones
        foreach($configurations as $internalName => $configuration) {
            if(!isset($existingConfigurations[$internalName])) {
                $configurationsToAdd[$internalName] = $configuration;
            }
        }

        //add the new configurations
        foreach($configurationsToAdd as $configuration) {
            $entity = new ConfigurationEntity(
                $configuration['name'],
                $configuration['type'],
                $configuration['definition']['default'] ?? null,
                $configuration['definition'],
                $configuration['folder'],
            );
            $this->configurationRepository->upsert($entity);
        }

        return count($configurationsToAdd);
    }

    protected function loadExistingConfigurations(): array {
        $configEntities = $this->configurationRepository->search(new Criteria())->getArray();

        $mappedConfigEntities = [];
        /** @var ConfigurationEntity $configEntity */
        foreach($configEntities as $configEntity) {
            $mappedConfigEntities[$configEntity->getInternalName()] = $configEntity;
        }

        return $mappedConfigEntities;
    }

    protected function loadConfigurationFiles(): array {
        $configurations = [];

        /** @var ModuleEntity $module */
        foreach(ListModulesCommand::getModuleList()->getArray() as $module) {

            $configFile = ROOT . $module->getPath() . '/config.xml';
            if(file_exists($configFile)) {
                $configurations = array_merge($configurations, $this->loadDataFromConfigurationFile($configFile));
            }
        }

        return $configurations;
    }

    protected function loadDataFromConfigurationFile(string $configFilePath): array {

        try {
            $content = XMLReader::readFile($configFilePath);
            if($content->getType() !== 'configuration') {
                return [];
            }
        }
        catch (Exception $e) {
            return [];
        }


        $configFields = [];

        /** @var XMLContentModel $collection */
        foreach($content->getChildren() as $collection) {
            $folder = $collection->getAttribute('folder', 'global');

            foreach($collection->getChildren() as $field) {
                try {
                    $fieldData = $this->readFieldData($field);
                    if(!empty($fieldData)) {
                        $fieldData['folder'] = $folder;
                        $configFields[$fieldData['name']] = $fieldData;
                    }
                }
                catch (MissingRequiredConfigurationFieldException $e) {
                    IO::printWarning($e->getMessage() . ' Skipping field!');
                }
            }
        }

        return $configFields;
    }


    /**
     * @throws MissingRequiredConfigurationFieldException
     */
    protected function readFieldData(XMLContentModel $field): array {
        $fieldData = [];

        /** @var XMLContentModel $nameElement */
        $nameElement = $field->getChildrenByType('name')->first();
        if(!$nameElement) {
            throw new MissingRequiredConfigurationFieldException('name', $field->getType());
        }
        $fieldData['name'] = $nameElement->getValue();

        switch($field->getType()) {
            case 'textfield':
                $fieldData['type'] = 'text';
                $this->getTextFieldData($field, $fieldData);
                break;
            case 'selectfield':
                $fieldData['type'] = 'select';
                $this->getSelectFieldData($field, $fieldData);
                break;
            case 'boolfield':
                $fieldData['type'] = 'bool';
                $this->getBoolFieldData($field, $fieldData);
                break;
            case 'entityselectfield':
                $fieldData['type'] = 'entity';
                $this->getEntitySelectFieldData($field, $fieldData);
                break;
            default:
                throw new MissingRequiredConfigurationFieldException('valid_type_err', 'valid_type_err');
                break;
        }

        return $fieldData;
    }

    protected function getTextFieldData(XMLContentModel $field, array &$fieldData): void {
        $defaultElement = $field->getChildrenByType('default')->first();
        $fieldData['definition']['default'] = $defaultElement ? $defaultElement->getValue() : '';
    }

    /**
     * @throws MissingRequiredConfigurationFieldException
     */
    protected function getSelectFieldData(XMLContentModel $field, array &$fieldData): void {
        $defaultElement = $field->getChildrenByType('default')->first();
        $fieldData['definition']['default'] = $defaultElement ? $defaultElement->getValue() : '';

        $multipleElement = $field->getChildrenByType('multiple')->first();
        $fieldData['definition']['multiple'] = $multipleElement ? ($multipleElement->getValue() == self::TRUE_VALUE) : false;

        /** @var XMLContentModel $optionsElement */
        $optionsElement = $field->getChildrenByType('options')->first();
        if(!$defaultElement) {
            throw new MissingRequiredConfigurationFieldException('options', $field->getType());
        }
        /** @var XMLContentModel[] $optionElements */
        $optionElements = $optionsElement->getChildrenByType('option');
        foreach($optionElements as $optionElement) {
            $fieldData['definition']['options'][] = [
                'value' => $optionElement->getAttribute('value', $optionElement->getValue()),
                'item' => $optionElement->getValue()
            ];
        }
    }

    protected function getBoolFieldData(XMLContentModel $field, array &$fieldData): void {
        $defaultElement = $field->getChildrenByType('default')->first();
        $fieldData['definition']['default'] =  ($defaultElement && $defaultElement->getValue() == self::TRUE_VALUE) ? 1 : 0;
    }


    /**
     * @throws MissingRequiredConfigurationFieldException
     */
    protected function getEntitySelectFieldData(XMLContentModel $field, array &$fieldData): void {
        $requiredElements = ['repository', 'identifier', 'identifier_getter', 'label'];

        foreach($requiredElements as $requiredElement) {
            /** @var XMLContentModel|null $element */
            $element = $field->getChildrenByType($requiredElement)->first();
            if(!$element) {
                throw new MissingRequiredConfigurationFieldException($requiredElement, $field->getType());
            }
            $fieldData['definition'][$requiredElement] = $element->getValue();
        }


        $searchField = $field->getChildrenByType('search')->first();
        if($searchField instanceof XMLContentModel) {
            $columns = $searchField->getChildrenByType('column');
            if($columns->count() < 1) {
                throw new MissingRequiredConfigurationFieldException('search -> option', $field->getType());
            }
            $fieldData['definition']['search'] = array_map(function(XMLContentModel $item) {
                return $item->getValue();
            }, $columns->getArray());

        }


    }

}