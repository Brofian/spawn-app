<?php

namespace spawnApp\Database\AdministratorTable;

use spawn\system\Core\Base\Database\Definition\Entity;
use spawn\system\Core\Base\Database\Definition\TableRepository;
use spawn\system\Core\Helper\UUID;

class AdministratorRepository extends TableRepository {

    public static function getEntityClass(): string
    {
        return AdministratorEntity::class;
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
        $updateValues['updatedAt'] = new \DateTime();

        return $updateValues;
    }

    protected function adjustEntityAfterSuccessfulUpdate(Entity $entity, array $updatedValues): void
    {
        /** @var AdministratorEntity $entity */
        $entity->setUpdatedAt($updatedValues['updatedAt']);
    }

    protected function prepareValuesForInsert(array $values): array
    {
        $now = new \DateTime();

        $values['id'] = UUID::randomBytes();
        $values['createdAt'] = $now;
        $values['updatedAt'] = $now;

        return $values;
    }

    protected function adjustEntityAfterSuccessfulInsert(Entity $entity, array $insertedValues): void
    {
        /** @var AdministratorEntity $entity */
        //set the id after the insert command in case of an error
        $entity->setId(UUID::bytesToHex($insertedValues['id']));
        $entity->setCreatedAt($insertedValues['createdAt']);
        $entity->setUpdatedAt($insertedValues['updatedAt']);
    }
}