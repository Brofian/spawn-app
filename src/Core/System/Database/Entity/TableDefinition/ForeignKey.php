<?php

namespace SpawnCore\System\Database\Entity\TableDefinition;

class ForeignKey {

    protected string $foreignTableName;
    protected array $foreignColumnNames;
    protected bool $onUpdateCascade;
    protected bool $onDeleteCascade;

    /**
     * ForeignKey constructor.
     * @param string|array $foreignColumnName
     * @throws InvalidForeignKeyConstraintException
     */
    public function __construct(
        string $foreignTableName,
        $foreignColumnName,
        bool $onUpdateCascade = true,
        bool $onDeleteCascade = false
    )
    {
        $this->foreignTableName = $foreignTableName;
        $this->onUpdateCascade = $onUpdateCascade;
        $this->onDeleteCascade = $onDeleteCascade;

        if(is_string($foreignColumnName))       $this->foreignColumnNames = [$foreignColumnName];
        elseif(is_array($foreignColumnName))    $this->foreignColumnNames =  $foreignColumnName;
        else                                    throw new InvalidForeignKeyConstraintException('Invalid column definition type. Expected string or array, got ' . get_debug_type($foreignColumnName));
    }

    public function getForeignTableName(): string {
        return $this->foreignTableName;
    }

    public function getForeignColumnNames(): array {
        return $this->foreignColumnNames;
    }

    public function getOptions(): array {
        return [
            'onUpdate' => $this->onUpdateCascade ? 'CASCADE' : 'NULL',
            'onDelete' => $this->onDeleteCascade ? 'CASCADE' : 'SET NULL'
        ];
    }

}