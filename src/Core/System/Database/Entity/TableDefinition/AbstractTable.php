<?php declare(strict_types = 1);
namespace SpawnCore\System\Database\Entity\TableDefinition;

use bin\spawn\IO;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\Comparator;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\SchemaException;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Type;
use SpawnCore\System\Custom\Gadgets\Slugifier;
use SpawnCore\System\Custom\Throwables\DatabaseConnectionException;
use SpawnCore\System\Database\Entity\TableDefinition\Constants\ColumnTypeOptions;
use SpawnCore\System\Database\Helpers\DatabaseConnection;


abstract class AbstractTable {

    public const PRIMARY_KEY_PREFIX = 'PK_';
    public const UNIQUE_INDEX_PREFIX = 'UI_';
    public const FOREIGN_KEY_PREFIX = 'FK_';

    /**
     * @return AbstractColumn[]
     */
    abstract public function getTableColumns(): array;

    abstract public function getTableName(): string;

    /**
     * @throws DatabaseConnectionException
     * @throws Exception
     */
    final public function upsertTable(): bool
    {

        try {
            $connection = DatabaseConnection::getConnection();

            $schema = $connection->createSchemaManager()->createSchema();
            $oldSchema = clone $schema;
            $tableName = $this->toDatabaseTableName($this->getTableName());

            if($schema->hasTable($tableName)) {
                //update
                IO::printLine(IO::TAB.":: Updating Table \"$tableName\"", IO::YELLOW_TEXT, 1);
                $this->updateTable($schema);
            }
            else {
                //create
                IO::printLine(IO::TAB.":: Creating Table \"$tableName\"", IO::YELLOW_TEXT);
                $this->createTable($schema);
            }


            $schemaDiff = Comparator::compareSchemas($oldSchema, $schema);
            $schemaDiffSql = $schemaDiff->toSql($connection->getDatabasePlatform());
            //$schemaDiffSql = array with all necessary sql queries


            foreach($schemaDiffSql as $sqlQuery) {
                $connection->executeQuery($sqlQuery);
            }
            $steps = count($schemaDiffSql);

            IO::printLine(IO::TAB.":: Updated table \"$tableName\" in $steps Steps!", IO::GREEN_TEXT, $steps ? 0 : 1);
        }
        catch(Exception|DatabaseConnectionException|\Exception $e) {
            IO::printLine(IO::TAB.":: Error! Could not create or update table \"$tableName\"!", IO::RED_TEXT);
            throw $e;
        }

        return false;
    }

    /**
     * @throws Exception
     * @throws SchemaException
     * @throws \Exception
     */
    final protected function updateTable(Schema $schema): void
    {
        try {
            $table = $schema->getTable($this->toDatabaseTableName($this->getTableName()));

            $columnNames = [];
            foreach($this->getTableColumns() as $column) {
                $columnName = $this->toDatabaseColumnName($column->getName());
                $columnNames[] = $columnName;

                if($table->hasColumn($columnName)) {

                    if(!$this->compareColumnWithDefinition($table, $table->getColumn($columnName), $column)) {

                        IO::printLine(IO::TAB.IO::TAB.':: Updating Column '. $columnName, IO::YELLOW_TEXT, 1);
                        //update column (^= remove and add the column again)
                        $this->dropColumnFromTable($table, $table->getColumn($columnName));
                        $this->createColumnInTable($schema, $table, $column);
                    }
                }
                else {
                    //create column
                    IO::printLine(IO::TAB.IO::TAB.':: Creating Column '. $columnName, IO::YELLOW_TEXT, 1);
                    $this->createColumnInTable($schema, $table, $column);
                }
            }


            //remove old columns
            $currentColumns = $table->getColumns();
            foreach($currentColumns as $currentColumn) {
                $currentColumnName = $currentColumn->getName();

                if(!in_array($currentColumnName, $columnNames, true)) {
                    IO::printLine(IO::TAB.IO::TAB.':: Removing Column '. $currentColumnName, IO::YELLOW_TEXT, 1);

                    $this->dropColumnFromTable($table, $currentColumn);
                }
            }
        }
        catch(SchemaException $schemaException) {
            throw $schemaException;
        } catch (Exception $e) {
            throw $e;
        } catch (\Exception $e) {
            throw $e;
        }

    }

    /**
     * @throws InvalidForeignKeyConstraintException
     * @throws SchemaException
     */
    final protected function createTable(Schema $schema): void
    {
        try {
            $schema->createTable($this->getTableName());
            $newTable = $schema->getTable($this->getTableName());

            foreach($this->getTableColumns() as $column) {
                IO::printLine(IO::TAB.IO::TAB.':: Creating Column '. $this->toDatabaseColumnName($column->getName()), IO::YELLOW_TEXT, 1);
                $this->createColumnInTable($schema, $newTable, $column);
            }
        }
        catch (SchemaException $schemaException) {
            throw $schemaException;
        }
    }

    /**
     * @throws Exception
     * @throws SchemaException
     */
    final protected function dropColumnFromTable(Table $table, Column $column): void
    {
        $currentColumnName = $column->getName();

        try {
            //drop foreign key
            $foreignKey = $this->toForeignKey($table->getName(), $currentColumnName);
            if($table->hasForeignKey($foreignKey)) {
                $table->removeForeignKey($foreignKey);
            }

            //drop indices
            $uniqueIndex = $this->toUniqueIndex($table->getName(), $currentColumnName);
            if($table->hasIndex($uniqueIndex)) {
                $table->dropIndex($uniqueIndex);
            }

            //drop primary key and primary key index
            if(array_key_exists($currentColumnName, $table->getPrimaryKeyColumns())) {
                $table->dropPrimaryKey();
            }


            $table->dropColumn($currentColumnName);
        }
        catch (SchemaException $e) {
            throw $e;
        } catch (Exception $e) {
            throw $e;
        }

    }


    /**
     * @throws InvalidForeignKeyConstraintException
     * @throws SchemaException
     */
    final protected function createColumnInTable(Schema $schema, Table $table, AbstractColumn $column): void
    {
        try {
            $columnName = $this->toDatabaseColumnName($column->getName());

            $table->addColumn($columnName, $column->getType(), $column->getOptions());

            if($column->isPrimaryKey() && !$table->hasPrimaryKey()) {
                IO::printLine(IO::TAB.IO::TAB.IO::TAB.':: Adding Primary Key for '. $columnName, IO::YELLOW_TEXT, 2);
                $table->setPrimaryKey([$columnName]);
            }
            else if($column->isUnique()) {
                IO::printLine(IO::TAB.IO::TAB.IO::TAB.':: Adding Unique Index for '. $columnName, IO::YELLOW_TEXT, 2);
                $table->addUniqueIndex($column->getCombinedUniqueColumns(), $this->toUniqueIndex($table->getName(), $columnName));
            }

            if($column->getForeignKeyConstraint()) {
                IO::printLine(IO::TAB.IO::TAB.IO::TAB.':: Adding Foreign Key Constraint for '. $columnName, IO::YELLOW_TEXT, 2);

                $foreignKeyConstraintData = $column->getForeignKeyConstraint();
                if($foreignKeyConstraintData !== null) {

                    $remoteTableName = $foreignKeyConstraintData->getForeignTableName();
                    $remoteColumnNames = $foreignKeyConstraintData->getForeignColumnNames();
                    $foreignKeyOptions = $foreignKeyConstraintData->getOptions();

                    if($schema->hasTable($remoteTableName)) {
                        $remoteTable = $schema->getTable($remoteTableName);

                        foreach($remoteColumnNames as $remoteColumnName) {
                            if(!$remoteTable->hasColumn($remoteColumnName)) {
                                throw new InvalidForeignKeyConstraintException("Tried adding a foreign key to non existent column \"$remoteColumnName\" of table \"$remoteTableName\" ");
                            }
                        }

                        $table->addForeignKeyConstraint(
                            $remoteTable,
                            [$columnName],
                            $remoteColumnNames,
                            $foreignKeyOptions,
                            $this->toForeignKey($table->getName(), $columnName)
                        );
                    }
                }
            }
        }
        catch(SchemaException $schemaException) {
            throw $schemaException;
        }


    }


    /**
     * @return bool
     * @throws \Exception
     */
    protected function compareColumnWithDefinition(Table $table, Column $columnActive, AbstractColumn $columnDefinition): bool {

        try {
            $platform = DatabaseConnection::getConnection()->getDriver()->getDatabasePlatform();
            $declaredTypeForDriver = Type::getType($columnDefinition->getType())->getSQLDeclaration([], $platform);
            $currentTypeForDriver = $columnActive->getType()->getSQLDeclaration([], $platform);

            $isEqual = (
                //type
                $declaredTypeForDriver === $currentTypeForDriver  &&
                //is unique
                $columnDefinition->isUnique() === $table->hasIndex($this->toUniqueIndex($table->getName(), $columnActive->getName())) &&
                //default value
                $columnDefinition->getDefault() === $columnActive->getDefault() &&
                //is primary key
                $columnDefinition->isPrimaryKey() === array_key_exists($columnActive->getName(), $table->getPrimaryKeyColumns())
            );

            if($isEqual) {

                $columnDefinitionOptions = $columnDefinition->getOptions();
                $optionsGetterSetter = ColumnTypeOptions::OPTION_GETTER_SETTER;

                foreach($columnDefinitionOptions as $option => $desiredValue) {
                    if(isset($optionsGetterSetter[$option])) {
                        $getter = $optionsGetterSetter[$option][0];

                        $optionEquals = $columnActive->$getter() === $desiredValue;

                        if(!$optionEquals) {
                            $isEqual = false;
                            break;
                        }
                    }
                }
            }
        }
        catch (\Exception $e) {
            throw $e;
        }

        return $isEqual;
    }



    protected function toDatabaseTableName(string $string): string {
        return Slugifier::toSnakeCase($string);
    }

    protected function toDatabaseColumnName(string $string): string {
        return Slugifier::toCamelCase($string);
    }

    protected function toPrimaryKey(string $table, string $column): string {
        return self::PRIMARY_KEY_PREFIX.$table.'_'.$column;
    }

    protected function toForeignKey(string $table, string $column): string {
        return self::FOREIGN_KEY_PREFIX.$table.'_'.$column;
    }

    protected function toUniqueIndex(string $table, string $column): string {
        return self::UNIQUE_INDEX_PREFIX.$table.'_'.$column;
    }



}