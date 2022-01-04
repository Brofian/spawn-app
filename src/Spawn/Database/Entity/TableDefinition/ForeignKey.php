<?php

namespace spawnCore\Database\Entity\TableDefinition;

class ForeignKey {

    protected string $foreignTableName;
    protected string $foreignColumnName;
    protected bool $onUpdateCascade;
    protected bool $onDeleteCascade;

    public function __construct(
        string $foreignTableName,
        string $foreignColumnName,
        bool $onUpdateCascade = true,
        bool $onDeleteCascade = false
    )
    {
        $this->foreignTableName = $foreignTableName;
        $this->foreignColumnName = $foreignColumnName;
        $this->onUpdateCascade = $onUpdateCascade;
        $this->onDeleteCascade = $onDeleteCascade;
    }

    public function getForeignTableName(): string {
        return $this->foreignTableName;
    }

    public function getForeignColumnName(): string {
        return $this->foreignColumnName;
    }

    public function getOptions(): array {
        return [
            'onUpdate' => $this->onUpdateCascade ? 'CASCADE' : 'NULL',
            'onDelete' => $this->onDeleteCascade ? 'CASCADE' : 'SET NULL'
        ];
    }

}