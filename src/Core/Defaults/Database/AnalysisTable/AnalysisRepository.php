<?php declare(strict_types = 1);
namespace SpawnCore\Defaults\Database\AnalysisTable;

use DateTime;
use Exception;
use SpawnCore\System\Custom\Gadgets\UUID;
use SpawnCore\System\Database\Entity\Entity;
use SpawnCore\System\Database\Entity\TableRepository;

class AnalysisRepository extends TableRepository {

    public static function getEntityClass(): string
    {
        return AnalysisEntity::class;
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
        $updateValues['urlId'] = $updateValues['urlId'] ? UUID::hexToBytes($updateValues['urlId']) : null;

        return $updateValues;
    }

    protected function adjustEntityAfterSuccessfulUpdate(Entity $entity, array $updatedValues): void
    {

    }

    /**
     * @param array $values
     * @return array
     * @throws Exception
     */
    protected function prepareValuesForInsert(array $values): array
    {
        $now = new DateTime("midnight");

        $values['id'] = UUID::randomBytes();
        $values['urlId'] = $values['urlId'] ? UUID::hexToBytes($values['urlId']) : null;
        $values['createdAt'] = $now;

        return $values;
    }

    protected function adjustEntityAfterSuccessfulInsert(Entity $entity, array $insertedValues): void
    {
        /** @var AnalysisEntity $entity */
        //set the id after the insert command in case of an error
        $entity->setId(UUID::bytesToHex($insertedValues['id']));
        $entity->setUrlId($insertedValues['urlId'] ? UUID::bytesToHex($insertedValues['urlId']) : null);
        $entity->setCreatedAt($insertedValues['createdAt']);
    }

    protected function adjustValuesAfterSelect(array &$values): void
    {
        $values['id'] = UUID::bytesToHex($values['id']);
        $values['urlId'] = $values['urlId'] ? UUID::bytesToHex($values['urlId']) : null;
    }


}