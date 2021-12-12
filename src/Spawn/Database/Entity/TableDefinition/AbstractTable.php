<?php

namespace spawnCore\Database\Entity\TableDefinition;

use bin\spawn\IO;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\SchemaException;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Type;
use spawn\system\Core\Base\Database\DatabaseConnection;
use spawnCore\Database\Entity\TableDefinition\Constants\ColumnTypeOptions;
use spawn\system\Core\Helper\Slugifier;


abstract class AbstractTable {

    public const PRIMARY_KEY_PREFIX = 'PK_';
    public const UNIQUE_INDEX_PREFIX = 'UI_';
    public const FOREIGN_KEY_PREFIX = 'FK_';

    /**
     * @return AbstractColumn[]
     */
    abstract function getTableColumns(): array;

    abstract function getTableName(): string;

    public final function upsertTable() {

        $connection = DatabaseConnection::getConnection();

        try {
            $schema = $connection->createSchemaManager()->createSchema();
            $oldSchema = clone $schema;
            $tableName = $this->toDatabaseTableName($this->getTableName());

            if($schema->hasTable($tableName)) {
                //update
                IO::printLine(IO::TAB.":: Updating Table \"$tableName\"", IO::YELLOW_TEXT);
                $this->updateTable($schema);
            }
            else {
                //create
                IO::printLine(IO::TAB.":: Creating Table \"$tableName\"", IO::YELLOW_TEXT);
                $this->createTable($schema);
            }


            $schemaDiffSql = $oldSchema->getMigrateToSql($schema, $connection->getDatabasePlatform());
            //$schemaDiffSql = array with all necessary sql queries


            foreach($schemaDiffSql as $sqlQuery) {
                $connection->executeQuery($sqlQuery);
            }
            $steps = count($schemaDiffSql);

            IO::printLine(IO::TAB.":: Updated table \"$tableName\" in $steps Steps!", IO::GREEN_TEXT);
        }
        catch(Exception $e) {
            IO::printLine(IO::TAB.":: Error! Could not create or update table \"$tableName\"!", IO::RED_TEXT);
            throw $e;
        }


        return false;
    }

    protected final function updateTable(Schema $schema) {
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

                if(!in_array($currentColumnName, $columnNames)) {
                    IO::printLine(IO::TAB.IO::TAB.':: Removing Column '. $currentColumnName, IO::YELLOW_TEXT, 1);

                    $this->dropColumnFromTable($table, $currentColumn);
                }
            }
        }
        catch(SchemaException $schemaException) {
            throw $schemaException;
        } catch (Exception $e) {
            throw $e;
        }

    }

    protected final function createTable(Schema $schema) {
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

    protected final function dropColumnFromTable(Table $table, Column $column) {
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
            if(in_array($currentColumnName, array_keys($table->getPrimaryKeyColumns()))) {
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


    protected final function createColumnInTable(Schema $schema, Table $table, AbstractColumn $column) {
        try {
            $columnName = $this->toDatabaseColumnName($column->getName());

            $table->addColumn($columnName, $column->getType(), $column->getOptions());

            if($column->isPrimaryKey() && $table->hasPrimaryKey() == false) {
                IO::printLine(IO::TAB.IO::TAB.IO::TAB.':: Adding Primary Key for '. $columnName, IO::YELLOW_TEXT, 2);
                $table->setPrimaryKey([$columnName]);
            }
            else if($column->isUnique()) {
                IO::printLine(IO::TAB.IO::TAB.IO::TAB.':: Adding Unique Index for '. $columnName, IO::YELLOW_TEXT, 2);
                $table->addUniqueIndex([$columnName], $this->toUniqueIndex($table->getName(), $columnName));
            }

            if($column->getForeignKeyConstraint()) {
                IO::printLine(IO::TAB.IO::TAB.IO::TAB.':: Adding Foreign Key Constraint for '. $columnName, IO::YELLOW_TEXT, 2);

                $foreignKeyConstraintData = $column->getForeignKeyConstraint();
                $remoteTableName = $foreignKeyConstraintData->getForeignTableName();
                $remoteColumnName = $foreignKeyConstraintData->getForeignColumnName();
                $foreignKeyOptions = $foreignKeyConstraintData->getOptions();

                if($schema->hasTable($remoteTableName)) {
                    $remoteTable = $schema->getTable($remoteTableName);

                    if ($remoteTable->hasColumn($remoteColumnName)) {
                        $table->addForeignKeyConstraint(
                            $remoteTable,
                            [$columnName],
                            [$remoteColumnName],
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

    protected function compareColumnWithDefinition(Table $table, Column $columnActive, AbstractColumn $columnDefinition): bool {
        $isEqual = false;

        try {
            $platform = DatabaseConnection::getConnection()->getDriver()->getDatabasePlatform();
            $declaredTypeForDriver = Type::getType($columnDefinition->getType())->getSQLDeclaration([], $platform);
            $currentTypeForDriver = $columnActive->getType()->getSQLDeclaration([], $platform);

            $isEqual = (
                //type
                $declaredTypeForDriver == $currentTypeForDriver  &&
                //is unique
                !!$columnDefinition->isUnique() == $table->hasIndex($this->toUniqueIndex($table->getName(), $columnActive->getName())) &&
                //default value
                $columnDefinition->getDefault() == $columnActive->getDefault() &&
                //is primary key
                !!$columnDefinition->isPrimaryKey() == in_array($columnActive->getName(), array_keys($table->getPrimaryKeyColumns()))
            );

            if($isEqual) {

                $columnDefinitionOptions = $columnDefinition->getOptions();
                $optionsGetterSetter = ColumnTypeOptions::OPTION_GETTER_SETTER;

                foreach($columnDefinitionOptions as $option => $desiredValue) {
                    if(isset($optionsGetterSetter[$option])) {
                        $getter = $optionsGetterSetter[$option][0];

                        $optionEquals = $columnActive->$getter() == $desiredValue;

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