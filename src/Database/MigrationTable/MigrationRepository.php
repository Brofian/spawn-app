<?php

namespace spawnApp\Database\MigrationTable;

use spawnCore\Database\Entity\Entity;
use spawnCore\Database\Entity\TableRepository;
use spawn\system\Core\Helper\UUID;

class MigrationRepository extends TableRepository {

    public static function getEntityClass(): string
    {
        return MigrationEntity::class;
    }

    protected function prepareValuesForInsert(array $values): array
    {
        $uuid = UUID::randomBytes();
        $now = new \DateTime();

        $values['id'] = $uuid;
        $values['createdAt'] = $now;
        $values['updatedAt'] = $now;

        return $values;
    }

    protected function adjustEntityAfterSuccessfulInsert(Entity $entity, array $insertedValues): void
    {
        /** @var MigrationEntity $entity */

        //set the id after the insert command in case of an error
        $entity->setId(UUID::bytesToHex($insertedValues['id']));
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
        $updateValues['updatedAt'] = new \DateTime();

        return $updateValues;
    }

    protected function adjustEntityAfterSuccessfulUpdate(Entity $entity, array $updatedValues): void {
        /** @var MigrationEntity $entity */

        $entity->setUpdatedAt($updatedValues['updatedAt']);
    }
}