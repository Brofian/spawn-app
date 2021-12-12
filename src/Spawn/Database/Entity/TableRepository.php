<?php declare(strict_types=1);

namespace spawnCore\Database\Entity;


use Doctrine\DBAL\Exception;
use spawnCore\Custom\Gadgets\UUID;
use spawnCore\Custom\Throwables\WrongEntityForRepositoryException;
use spawnCore\Database\Entity\TableDefinition\AbstractTable;
use spawnCore\Database\Helpers\DatabaseConnection;

abstract class TableRepository
{

    protected array $tableColumns = [];
    protected string $tableName;

    abstract public static function getEntityClass(): string;

    abstract protected function getUpdateFilterColumnsFromValues(array $updateValues): array;

    abstract protected function prepareValuesForUpdate(array $updateValues): array;

    abstract protected function adjustEntityAfterSuccessfulUpdate(Entity $entity, array $updatedValues): void;

    abstract protected function prepareValuesForInsert(array $values): array;

    abstract protected function adjustEntityAfterSuccessfulInsert(Entity $entity, array $insertedValues): void;

    public function __construct(
        AbstractTable $tableDefinition
    )
    {
        foreach($tableDefinition->getTableColumns() as $tableColumn) {
            $this->tableColumns[$tableColumn->getName()] = $tableColumn->getTypeIdentifier();
        }

        $this->tableName = $tableDefinition->getTableName();
    }

    public function search(array $where = [], int $limit = 10000, int $offset = 0) : EntityCollection {
        $conn = DatabaseConnection::getConnection();
        $qb = $conn->createQueryBuilder();
        $query = $qb->select('*')->from($this->tableName)->setMaxResults($limit)->setFirstResult($offset);
        $whereFunction = 'where';
        foreach($where as $column => $value) {
            if(is_string($value)) {
                $query->$whereFunction("$column LIKE ?");
            }
            else if(is_array($value) && isset($value['operator'], $value['value'])) {
                $query->$whereFunction("$column ".$value['operator']." ?");
                $where[$column] = $value['value'];
            }
            else {
                $query->$whereFunction("$column = ?");
            }

            $whereFunction = 'andWhere';
        }

        /** @var EntityCollection $entityCollection */
        $entityCollection = new EntityCollection($this->getEntityClass());

        try {
            $stmt = $conn->prepare($query->getSQL());
            $count = 1;
            foreach($where as $column => $value) {
                $stmt->bindValue($count, $value);
                $count++;
            }

            $queryResult = $stmt->executeQuery();

            while($row = $queryResult->fetchAssociative()) {
                if(isset($row['id'])) {
                    $row['id'] = UUID::bytesToHex($row['id']);
                }
                $entityCollection->add($this->arrayToEntity($row));
            }
        } catch (Exception $e) {
            if(MODE == 'dev') { dd($e); }
            return $entityCollection;
        }


        return $entityCollection;
    }


    public function delete(array $where) {
        if(empty($where)) {
            return false;
        }

        $conn = DatabaseConnection::getConnection();
        $qb = $conn->createQueryBuilder();
        $query = $qb->delete($this->tableName);

        $whereFunction = 'where';
        foreach($where as $column => $value) {
            if(is_string($value)) {
                $query->$whereFunction("$column LIKE ?");
            }
            elseif(is_array($value) && !empty($value)) {
                $placeholders = str_split(str_repeat('?', count($value)));
                $query->$whereFunction("$column IN (".implode(',', $placeholders).")");
            }
            else {
                $query->$whereFunction("$column = ?");
            }

            $whereFunction = 'andWhere';
        }

        try {
            $stmt = $conn->prepare($query->getSQL());
            $count = 1;
            foreach($where as $column => $value) {

                if(is_array($value)) {
                    foreach($value as $v) {
                        $stmt->bindValue($count, $v);
                        $count++;
                    }
                }
                else {
                    $stmt->bindValue($count, $value);
                    $count++;
                }
            }

            $stmt->executeQuery();
        } catch (Exception $e) {
            if(MODE == 'dev') { dd($e); }
            return false;
        }

        return true;
    }

    public function arrayToEntity(array $values): Entity {
        /** @var Entity $entityClass */
        $entityClass = $this->getEntityClass();
        return $entityClass::getEntityFromArray($values);
    }


    /**
     * @param Entity $entity
     * @return bool
     * @throws Exception
     * @throws WrongEntityForRepositoryException
     */
    public function upsert(Entity $entity): bool {
        $this->verifyEntityClass($entity);

        if($entity->getId() === null) {
            return $this->insert($entity);
        }
        else {
            return $this->update($entity);
        }
    }

    /**
     * @param Entity $entity
     * @return bool
     * @throws Exception
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
     * @param Entity $entity
     * @return bool
     * @throws Exception
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

    protected function getTypeIdentifiersForColumns(array $columns): array {
        $identifiers = [];
        foreach($columns as $column) {
            if(isset($this->tableColumns[$column])) {
               $identifiers[] = $this->tableColumns[$column];
            }
            else {
                $identifiers[] = \PDO::PARAM_NULL;
            }
        }
        return $identifiers;
    }

    protected function verifyEntityClass(Entity $entity) {
        $desiredEntityClass = $this->getEntityClass();
        if(!($entity instanceof $desiredEntityClass)) {
            throw new WrongEntityForRepositoryException(get_class($entity), $desiredEntityClass, self::class);
        }
    }


}