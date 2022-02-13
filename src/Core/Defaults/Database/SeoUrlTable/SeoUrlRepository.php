<?php

namespace SpawnCore\Defaults\Database\SeoUrlTable;

use DateTime;
use Exception;
use SpawnCore\System\Custom\Gadgets\UUID;
use SpawnCore\System\Database\Entity\Entity;
use SpawnCore\System\Database\Entity\TableRepository;

class SeoUrlRepository extends TableRepository {

    public static function getEntityClass(): string
    {
        return SeoUrlEntity::class;
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
        $values['parameters'] = json_encode($values['parameters']);
        $values['createdAt'] = $now;
        $values['updatedAt'] = $now;

        return $values;
    }

    protected function adjustEntityAfterSuccessfulInsert(Entity $entity, array $insertedValues): void
    {
        /** @var SeoUrlEntity $entity */

        //set the id after the insert command in case of an error
        $entity->setId(UUID::bytesToHex($insertedValues['id']));
        $entity->setParameters(json_decode($insertedValues['parameters'], true));
        $entity->setCreatedAt($insertedValues['createdAt']);
        $entity->setUpdatedAt($insertedValues['updatedAt']);
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
        $updateValues['parameters'] = json_encode($updateValues['parameters']);
        $updateValues['updatedAt'] = new DateTime();

        return $updateValues;
    }

    protected function adjustEntityAfterSuccessfulUpdate(Entity $entity, array $updatedValues): void {
        /** @var SeoUrlEntity $entity */

        $entity->setParameters(json_decode($updatedValues['parameters'], true));
        $entity->setUpdatedAt($updatedValues['updatedAt']);
    }


    protected function adjustValuesAfterSelect(array &$values): void
    {
        $values['id'] = UUID::bytesToHex($values['id']);
    }

}