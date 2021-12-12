<?php

namespace spawnCore\Database\Entity\TableDefinition\DefaultColumns;


use PDO;
use spawnCore\Database\Entity\TableDefinition\AbstractColumn;
use spawnCore\Database\Entity\TableDefinition\Constants\ColumnTypes;
use spawnCore\Database\Entity\TableDefinition\ForeignKey;

class StringColumn extends AbstractColumn {

    protected string $columnName;
    protected ?bool $canBeNull;
    protected ?string $default;
    protected bool $unique;
    protected ?int $length;
    protected ?bool $hasFixedLength;
    protected ?ForeignKey $foreignKey;


    public function __construct(
        string $columnName,
        ?bool $canBeNull = null,
        ?string $default = null,
        bool $unique = false,
        ?int $maxLength = null,
        ?bool $hasFixedLength = false,
        ?ForeignKey $foreignKey = null
    )
    {
        $this->columnName = $columnName;
        $this->canBeNull = $canBeNull;
        $this->default = $default;
        $this->unique = $unique;
        $this->hasFixedLength = $hasFixedLength;
        $this->length = $maxLength;
        $this->foreignKey = $foreignKey;
    }


    public function getName(): string
    {
        return $this->columnName;
    }

    public function getType(): string
    {
        return ($this->length !== null || $this->default !== null) ? ColumnTypes::STRING : ColumnTypes::TEXT;
    }

    public function isUnique(): bool
    {
        return $this->unique;
    }

    public function hasFixedLength(): ?bool
    {
        return $this->hasFixedLength;
    }

    public function getDefault()
    {
        return $this->default;
    }

    public function canBeNull(): ?bool
    {
        return $this->canBeNull;
    }

    public function getLength(): ?int {
        return $this->length;
    }

    public function getForeignKeyConstraint(): ?ForeignKey
    {
        return $this->foreignKey;
    }


    public function getTypeIdentifier()
    {
        return PDO::PARAM_STR;
    }
}