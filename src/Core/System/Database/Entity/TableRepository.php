<?php declare(strict_types=1);

namespace SpawnCore\System\Database\Entity;


use Doctrine\DBAL\Exception;
use PDO;
use SpawnCore\System\Custom\Gadgets\UUID;
use SpawnCore\System\Custom\Throwables\DatabaseConnectionException;
use SpawnCore\System\Custom\Throwables\WrongEntityForRepositoryException;
use SpawnCore\System\Database\Criteria\Criteria;
use SpawnCore\System\Database\Entity\TableDefinition\AbstractTable;
use SpawnCore\System\Database\Entity\TableDefinition\DefaultColumns\JsonColumn;
use SpawnCore\System\Database\Entity\TableDefinition\DefaultColumns\UuidColumn;
use SpawnCore\System\Database\Helpers\DatabaseConnection;

class TableRepository
{
    protected array $tableColumns = [];
    protected AbstractTable $tableDefinition;

    /**
     * TableRepository constructor.
     * @param AbstractTable $tableDefinition
     */
    public function __construct(
        AbstractTable $tableDefinition
    )
    {
        foreach($tableDefinition->getTableColumns() as $tableColumn) {
            $this->tableColumns[$tableColumn->getName()] = $tableColumn->getTypeIdentifier();
        }

        $this->tableDefinition = $tableDefinition;
    }

    /**
     * @throws DatabaseConnectionException
     * @throws RepositoryException
     */
    public function search(Criteria $criteria, int $limit = 10000, int $offset = 0) : EntityCollection {
        $conn = DatabaseConnection::getConnection();
        $qb = $conn->createQueryBuilder();
        $query = $qb->select('*')->from($this->tableDefinition->getTableName())->setMaxResults($limit)->setFirstResult($offset);

        $criteria->computeRelations($query);

        $query->where($criteria->generateCriteria());

        foreach($criteria->getOrderBys() as $orderBy) {
            $query->addOrderBy($orderBy->getColumn(), $orderBy->getDirection());
        }

        $entityCollection = new EntityCollection($this->tableDefinition->getEntityClass());

        try {
            $stmt = $conn->prepare($query->getSQL());
            foreach($criteria->getParameters() as $id => $parameter) {
                $stmt->bindValue($id+1, $parameter);
            }

            $queryResult = $stmt->executeQuery();

            while($row = $queryResult->fetchAssociative()) {
                $this->adjustValuesAfterSelect($row);
                $entityCollection->add($this->arrayToEntity($row));
            }
        } catch (Exception $e) {
            throw new RepositoryException($e->getMessage(), $e);
        }


        $this->hydrateCollection($entityCollection, $criteria->getAssociations());

        return $entityCollection;
    }

    /**
     * @throws DatabaseConnectionException
     * @throws InvalidRepositoryInteractionException
     * @throws RepositoryException
     */
    public function delete(Criteria $criteria): bool
    {
        if(empty($criteria->getFilters())) {
            throw new InvalidRepositoryInteractionException('Tried deleting from database without filter');
        }


        $conn = DatabaseConnection::getConnection();
        $qb = $conn->createQueryBuilder();
        $query = $qb->delete($this->tableDefinition->getTableName());
        $query->where($criteria->generateCriteria());

        try {
            $stmt = $conn->prepare($query->getSQL());
            foreach($criteria->getParameters() as $id => $parameter) {
                $stmt->bindValue($id+1, $parameter);

            }

            $stmt->executeQuery();
        } catch (Exception $e) {
            throw new RepositoryException($e->getMessage(), $e);
        }

        return true;
    }

    /**
     * @param Criteria $criteria
     * @return int
     * @throws DatabaseConnectionException
     * @throws RepositoryException
     */
    public function count(Criteria $criteria): int {
        $conn = DatabaseConnection::getConnection();
        $qb = $conn->createQueryBuilder();
        $query = $qb->select('COUNT(*) as count')->from($this->tableDefinition->getTableName());
        $query->where($criteria->generateCriteria());

        try {
             $stmt = $conn->prepare($query->getSQL());
             $result = $stmt->executeQuery($criteria->getParameters());
             return (int)$result->fetchAssociative()['count'];
        } catch (Exception $e) {
            throw new RepositoryException($e->getMessage(), $e);
        }
    }

    /**
     * @return Entity
     */
    public function arrayToEntity(array $values): Entity {
        /** @var Entity $entityClass */
        $entityClass = $this->tableDefinition->getEntityClass();
        return $entityClass::getEntityFromArray($values);
    }


    /**
     * @throws Exception
     * @throws WrongEntityForRepositoryException
     * @throws DatabaseConnectionException
     */
    public function upsert(Entity $entity): bool {
        $this->verifyEntityClass($entity);

        if($entity->has('id') === false) {
            return $this->insert($entity);
        }

        return $this->update($entity);
    }

    /**
     * @throws Exception
     * @throws DatabaseConnectionException
     */
    protected function insert(Entity $entity): bool {
        $entityArray = $entity->toArray();

        $entityArray = $this->prepareValuesForInsert($entityArray);

        DatabaseConnection::getConnection()->insert(
            $this->tableDefinition->getTableName(),
            $entityArray,
            $this->getTypeIdentifiersForColumns(array_keys($entityArray))
        );

        $this->adjustEntityAfterSuccessfulInsert($entity, $entityArray);

        return true;
    }

    /**
     * @throws Exception
     * @throws DatabaseConnectionException
     */
    protected function update(Entity $entity): bool {
        $entityArray = $entity->toArray();

        $filterColumns = $this->getUpdateFilterColumnsFromValues($entityArray);

        $entityArray = $this->prepareValuesForUpdate($entityArray);

        DatabaseConnection::getConnection()->update(
            $this->tableDefinition->getTableName(),
            $entityArray,
            $filterColumns,
            $this->getTypeIdentifiersForColumns(array_keys($entityArray))
        );

        $this->adjustEntityAfterSuccessfulUpdate($entity, $entityArray);

        return true;
    }

    public function getEntityClass(): string {
        return $this->tableDefinition->getEntityClass();
    }

    protected function getTypeIdentifiersForColumns(array $columns): array {
        $identifiers = [];
        foreach($columns as $column) {
            if(isset($this->tableColumns[$column])) {
               $identifiers[] = $this->tableColumns[$column];
            }
            else {
                $identifiers[] = PDO::PARAM_NULL;
            }
        }
        return $identifiers;
    }


    /**
     * @throws WrongEntityForRepositoryException
     */
    protected function verifyEntityClass(Entity $entity): void
    {
        $desiredEntityClass = $this->tableDefinition->getEntityClass();
        if(!($entity instanceof $desiredEntityClass)) {
            throw new WrongEntityForRepositoryException(get_class($entity), $desiredEntityClass);
        }
    }

    protected function hydrateCollection(EntityCollection $collection, array $associationChains): void {
        $tableAssociations = $this->tableDefinition->getTableAssociations();

        foreach($associationChains as $associationChain) {
            if(str_contains($associationChain, '.')) {
                [$association, $childAssociationChain] = explode('.', $associationChain, 2);
            }
            else {
                $association = $associationChain;
                $childAssociationChain = null;
            }

            foreach($tableAssociations as $tableAssociation) {
                if($tableAssociation->getOtherEntity() === $association) {
                    $tableAssociation->applyAssociation($collection, $childAssociationChain);
                }
            }
        }
    }


    protected function getUpdateFilterColumnsFromValues(array $updateValues): array {
        return [
            'id' => UUID::hexToBytes($updateValues['id'])
        ];
    }

    protected function prepareValuesForUpdate(array $updateValues): array {
        foreach($this->tableDefinition->getTableColumns() as $tableColumn) {
            if(isset($updateValues[$tableColumn->getName()])) {

                if($tableColumn instanceof UuidColumn) {
                    $updateValues[$tableColumn->getName()] = $updateValues[$tableColumn->getName()] ? UUID::hexToBytes($updateValues[$tableColumn->getName()]): null;
                }
                elseif($tableColumn instanceof JsonColumn) {
                    $updateValues[$tableColumn->getName()] = $updateValues[$tableColumn->getName()] ? json_encode($updateValues[$tableColumn->getName()], JSON_THROW_ON_ERROR): null;
                }
            }
        }

        return $updateValues;
    }

    protected function adjustEntityAfterSuccessfulUpdate(Entity &$entity, array $updatedValues): void {
        $entity = $entity::getEntityFromArray(array_merge($entity->toArray(), $updatedValues));
    }

    protected function prepareValuesForInsert(array $values): array {
        $now = new \DateTime();

        foreach($this->tableDefinition->getTableColumns() as $tableColumn) {
            $columnName = $tableColumn->getName();

            if($columnName === 'id') {
                $values[$columnName] = $values[$columnName] ?? UUID::randomBytes();
            }
            else if($columnName === 'updatedAt') {
                $values[$columnName] = $values[$columnName] ?? $now;
            }
            else if($columnName === 'createdAt') {
                $values[$columnName] = $values[$columnName] ?? $now;
            }
            else if($tableColumn instanceof UuidColumn && isset($values[$columnName])) {
                $values[$columnName] = UUID::hexToBytes($values[$columnName]);
            }
            else if($tableColumn instanceof JsonColumn && isset($values[$columnName])) {
                $values[$columnName] = json_encode($values[$columnName], JSON_THROW_ON_ERROR);
            }
        }

        return $values;
    }

    protected function adjustEntityAfterSuccessfulInsert(Entity &$entity, array $insertedValues): void {

        foreach($this->tableDefinition->getTableColumns() as $tableColumn) {
            $columnName = $tableColumn->getName();
            if($tableColumn instanceof UuidColumn) {
                $insertedValues[$columnName] = $insertedValues[$columnName] ? UUID::bytesToHex($insertedValues[$columnName]) : null;
            }
        }

        $entity = $entity::getEntityFromArray(array_merge($entity->toArray(), $insertedValues));
    }

    protected function adjustValuesAfterSelect(array &$values): void {
        foreach($this->tableDefinition->getTableColumns() as $tableColumn) {
            $columnName = $tableColumn->getName();
            if($tableColumn instanceof UuidColumn) {
                $values[$columnName] = $values[$columnName] ? UUID::bytesToHex($values[$columnName]) : null;
            }
        }
    }

}