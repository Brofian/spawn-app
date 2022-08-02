<?php

namespace SpawnCore\Defaults\Database\LanguageTable;

use DateTime;
use Exception;
use SpawnCore\System\Custom\Gadgets\UUID;
use SpawnCore\System\Database\Entity\Entity;
use SpawnCore\System\Database\Entity\TableDefinition\AbstractTable;
use SpawnCore\System\Database\Entity\TableRepository;

class LanguageRepository extends TableRepository {

    public function __construct(AbstractTable $tableDefinition)
    {
        parent::__construct($tableDefinition);
    }

    protected function getUpdateFilterColumnsFromValues(array $updateValues): array
    {
        return [
            'id' => UUID::hexToBytes($updateValues['id'])
        ];
    }

    protected function prepareValuesForUpdate(array $updateValues): array
    {
        $updateValues['id'] = UUID::hexToBytes($updateValues['id']);
        if($updateValues['parentId'] !== null) {
            $updateValues['parentId'] = UUID::hexToBytes($updateValues['parentId']);
        }
        $updateValues['updatedAt'] = new DateTime();

        return $updateValues;
    }

    protected function adjustEntityAfterSuccessfulUpdate(Entity $entity, array $updatedValues): void
    {
        /** @var LanguageEntity $entity */
        $entity->setUpdatedAt($updatedValues['updatedAt']);
    }

    /**
     * @param array $values
     * @return array
     * @throws Exception
     */
    protected function prepareValuesForInsert(array $values): array
    {
        $now = new DateTime();

        $values['id'] = UUID::randomBytes();
        if($values['parentId'] !== null) {
            $values['parentId'] = UUID::hexToBytes($values['parentId']);
        }
        $values['createdAt'] = $now;
        $values['updatedAt'] = $now;

        return $values;
    }

    protected function adjustEntityAfterSuccessfulInsert(Entity $entity, array $insertedValues): void
    {
        /** @var LanguageEntity $entity */
        //set the id after the insert command in case of an error
        $entity->setId(UUID::bytesToHex($insertedValues['id']));
        if($insertedValues['parentId'] !== null) {
            $entity->setParentId(UUID::bytesToHex($insertedValues['parentId']));
        }
        $entity->setCreatedAt($insertedValues['createdAt']);
        $entity->setUpdatedAt($insertedValues['updatedAt']);
    }

    protected function adjustValuesAfterSelect(array &$values): void
    {
        $values['id'] = UUID::bytesToHex($values['id']);
        if($values['parentId'] !== null) {
            $values['parentId'] = UUID::bytesToHex($values['parentId']);
        }
    }

}