<?php

namespace spawnCore\Database\Entity\TableDefinition;

class ForeignKey {

    protected string $foreignTableName;
    protected string $foreignColumnName;
    protected bool $onUpdateCascade;

    public function __construct(
        string $foreignTableName,
        string $foreignColumnName,
        bool $onUpdateCascade = true
    )
    {
        $this->foreignTableName = $foreignTableName;
        $this->foreignColumnName = $foreignColumnName;
        $this->onUpdateCascade = $onUpdateCascade;
    }

    public function getForeignTableName(): string {
        return $this->foreignTableName;
    }

    public function getForeignColumnName(): string {
        return $this->foreignColumnName;
    }

    public function getOptions(): array {
        return [
            'onUpdate' => $this->onUpdateCascade ? 'CASCADE' : 'NULL'
        ];
    }

}