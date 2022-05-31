<?php declare(strict_types = 1);

namespace SpawnCore\Defaults\Database\UserTable;

use DateTime;
use Exception;
use SpawnCore\System\Custom\Gadgets\UUID;
use SpawnCore\System\Database\Entity\Entity;
use SpawnCore\System\Database\Entity\TableDefinition\AbstractTable;
use SpawnCore\System\Database\Entity\TableRepository;

class UserRepository extends TableRepository {

    public static function getEntityClass(): string
    {
        return UserEntity::class;
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
        $updateValues['languageId'] = $updateValues['languageId'] ? UUID::hexToBytes($updateValues['languageId']) : null;
        $updateValues['updatedAt'] = new DateTime();

        return $updateValues;
    }

    protected function adjustEntityAfterSuccessfulUpdate(Entity $entity, array $updatedValues): void
    {
        /** @var UserEntity $entity */
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
        $values['languageId'] = $values['languageId'] ? UUID::hexToBytes($values['languageId']) : null;
        $values['createdAt'] = $now;
        $values['updatedAt'] = $now;

        return $values;
    }

    protected function adjustEntityAfterSuccessfulInsert(Entity $entity, array $insertedValues): void
    {
        /** @var UserEntity $entity */
        //set the id after the insert command in case of an error
        $entity->setId(UUID::bytesToHex($insertedValues['id']));
        $entity->setLanguageId($insertedValues['languageId'] ? UUID::hexToBytes($insertedValues['languageId']) : null);
        $entity->setCreatedAt($insertedValues['createdAt']);
        $entity->setUpdatedAt($insertedValues['updatedAt']);
    }

    protected function adjustValuesAfterSelect(array &$values): void
    {
        $values['id'] = UUID::bytesToHex($values['id']);
        $values['languageId'] = $values['languageId'] ? UUID::bytesToHex($values['languageId']) : null;
    }
}