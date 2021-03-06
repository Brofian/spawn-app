<?php declare(strict_types = 1);
namespace SpawnCore\Defaults\Database\ModuleTable;

use DateTime;
use Exception;
use SpawnCore\System\Custom\Gadgets\UUID;
use SpawnCore\System\Database\Entity\Entity;
use SpawnCore\System\Database\Entity\TableRepository;

class ModuleRepository extends TableRepository {

    public static function getEntityClass(): string
    {
        return ModuleEntity::class;
    }

    /**
     * @param array $values
     * @return array
     * @throws Exception
     */
    protected function prepareValuesForInsert(array $values): array
    {
        $uuid = UUID::randomBytes();
        $now = new DateTime();

        $values['id'] = $uuid;
        $values['createdAt'] = $now;
        $values['updatedAt'] = $now;

        return $values;
    }

    protected function adjustEntityAfterSuccessfulInsert(Entity $entity, array $insertedValues): void
    {
        /** @var ModuleEntity $entity */

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
        $updateValues['updatedAt'] = new DateTime();
        return $updateValues;
    }

    protected function adjustEntityAfterSuccessfulUpdate(Entity $entity, array $updatedValues): void
    {
        /** @var ModuleEntity $entity */
        $entity->setUpdatedAt($updatedValues['updatedAt']);
    }


    protected function adjustValuesAfterSelect(array &$values): void
    {
        $values['id'] = UUID::bytesToHex($values['id']);
    }

}