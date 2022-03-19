<?php declare(strict_types = 1);
namespace SpawnCore\Defaults\Database\SnippetTable;

use DateTime;
use Exception;
use SpawnCore\System\Custom\Gadgets\UUID;
use SpawnCore\System\Database\Entity\Entity;
use SpawnCore\System\Database\Entity\TableDefinition\AbstractTable;
use SpawnCore\System\Database\Entity\TableRepository;

class SnippetRepository extends TableRepository {

    public function __construct(AbstractTable $tableDefinition)
    {
        parent::__construct($tableDefinition);
    }


    public static function getEntityClass(): string
    {
        return SnippetEntity::class;
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
        $updateValues['languageId'] = UUID::hexToBytes($updateValues['languageId']);
        $updateValues['updatedAt'] = new DateTime();

        return $updateValues;
    }

    protected function adjustEntityAfterSuccessfulUpdate(Entity $entity, array $updatedValues): void
    {
        /** @var SnippetEntity $entity */
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
        $values['languageId'] = UUID::hexToBytes($values['languageId']);
        $values['createdAt'] = $now;
        $values['updatedAt'] = $now;

        return $values;
    }

    protected function adjustEntityAfterSuccessfulInsert(Entity $entity, array $insertedValues): void
    {
        /** @var SnippetEntity $entity */
        //set the id after the insert command in case of an error
        $entity->setId(UUID::bytesToHex($insertedValues['id']));
        $entity->setLanguageId(UUID::bytesToHex($insertedValues['languageId']));
        $entity->setCreatedAt($insertedValues['createdAt']);
        $entity->setUpdatedAt($insertedValues['updatedAt']);
    }

    protected function adjustValuesAfterSelect(array &$values): void
    {
        $values['id'] = UUID::bytesToHex($values['id']);
        $values['languageId'] = UUID::bytesToHex($values['languageId']);
    }

}