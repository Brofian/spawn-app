<?php declare(strict_types=1);

namespace SpawnCore\System\Database\Entity;


use Doctrine\DBAL\Exception;
use PDO;
use SpawnCore\System\Custom\Throwables\DatabaseConnectionException;
use SpawnCore\System\Custom\Throwables\WrongEntityForRepositoryException;
use SpawnCore\System\Database\Criteria\Criteria;
use SpawnCore\System\Database\Entity\TableDefinition\AbstractTable;
use SpawnCore\System\Database\Helpers\DatabaseConnection;

abstract class TableRepository
{

    protected array $tableColumns = [];
    protected string $tableName;
    /**  */
    abstract public static function getEntityClass(): string;
    /**  */
    abstract protected function getUpdateFilterColumnsFromValues(array $updateValues): array;
    /**  */
    abstract protected function prepareValuesForUpdate(array $updateValues): array;
    /**  */
    abstract protected function adjustEntityAfterSuccessfulUpdate(Entity $entity, array $updatedValues): void;
    /**  */
    abstract protected function prepareValuesForInsert(array $values): array;
    /**  */
    abstract protected function adjustEntityAfterSuccessfulInsert(Entity $entity, array $insertedValues): void;
    /**  */
    abstract protected function adjustValuesAfterSelect(array &$values): void;

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

        $this->tableName = $tableDefinition->getTableName();
    }

    /**
     * @throws DatabaseConnectionException
     * @throws RepositoryException
     */
    public function search(Criteria $criteria, int $limit = 10000, int $offset = 0) : EntityCollection {
        $conn = DatabaseConnection::getConnection();
        $qb = $conn->createQueryBuilder();
        $query = $qb->select('*')->from($this->tableName)->setMaxResults($limit)->setFirstResult($offset);

        $criteria->computeRelations($query);

        $query->where($criteria->generateCriteria());

        foreach($criteria->getOrderBys() as $orderBy) {
            $query->addOrderBy($orderBy->getColumn(), $orderBy->getDirection());
        }

        /** @var EntityCollection $entityCollection */
        $entityCollection = new EntityCollection(static::getEntityClass());

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
        $query = $qb->delete($this->tableName);
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
        $query = $qb->select('COUNT(*) as count')->from($this->tableName);
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
        $entityClass = static::getEntityClass();
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
            $this->tableName,
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
            $this->tableName,
            $entityArray,
            $filterColumns,
            $this->getTypeIdentifiersForColumns(array_keys($entityArray))
        );

        $this->adjustEntityAfterSuccessfulUpdate($entity, $entityArray);

        return true;
    }

    /**
     * @return array
     */
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
        $desiredEntityClass = static::getEntityClass();
        if(!($entity instanceof $desiredEntityClass)) {
            throw new WrongEntityForRepositoryException(get_class($entity), $desiredEntityClass, self::class);
        }
    }


}