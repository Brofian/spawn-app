<?php

namespace SpawnCore\System\Database\Entity\TableDefinition\DefaultColumns;


use PDO;
use SpawnCore\System\Database\Entity\TableDefinition\AbstractColumn;
use SpawnCore\System\Database\Entity\TableDefinition\Constants\ColumnTypes;
use SpawnCore\System\Database\Entity\TableDefinition\ForeignKey;

class StringColumn extends AbstractColumn {

    protected string $columnName;
    protected ?bool $canBeNull;
    protected ?string $default;
    protected ?int $length;
    protected ?bool $hasFixedLength;
    protected ?ForeignKey $foreignKey;


    /**
     * @param bool|string|array $unique
     */
    public function __construct(
        string $columnName,
        ?bool $canBeNull = null,
        ?string $default = null,
        $uniqueColumnCombination = false,
        ?int $maxLength = null,
        ?bool $hasFixedLength = false,
        ?ForeignKey $foreignKey = null
    )
    {
        $this->columnName = $columnName;
        $this->canBeNull = $canBeNull;
        $this->default = $default;
        $this->hasFixedLength = $hasFixedLength;
        $this->length = $maxLength;
        $this->foreignKey = $foreignKey;
        $this->setUniqueCombinedColumns($uniqueColumnCombination);
    }


    public function getName(): string
    {
        return $this->columnName;
    }

    public function getType(): string
    {
        return ($this->length !== null || $this->default !== null) ? ColumnTypes::STRING : ColumnTypes::TEXT;
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