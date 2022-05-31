<?php declare(strict_types = 1);
namespace SpawnCore\System\Database\Entity\TableDefinition\DefaultColumns;


use PDO;
use SpawnCore\System\Database\Entity\TableDefinition\AbstractColumn;
use SpawnCore\System\Database\Entity\TableDefinition\Constants\ColumnTypes;
use SpawnCore\System\Database\Entity\TableDefinition\ForeignKey;

class UuidColumn extends AbstractColumn {

    protected string $columnName;
    protected ?ForeignKey $foreignKey;
    protected bool $canBeNull;


    public function __construct(
        string $columnName,
        ?ForeignKey $foreignKey = null,
        bool $canBeNull = true
    )
    {
        $this->columnName = $columnName;
        $this->foreignKey = $foreignKey;
        $this->canBeNull = $canBeNull;
    }


    public function getName(): string
    {
        return $this->columnName;
    }

    public function getType(): string
    {
        return ColumnTypes::BINARY;
    }

    public function getLength(): ?int
    {
        return 24;
    }

    public function hasFixedLength(): ?bool
    {
        return true;
    }

    public function canBeNull(): ?bool
    {
        return $this->canBeNull;
    }

    public function getForeignKeyConstraint(): ?ForeignKey
    {
        return $this->foreignKey;
    }

    public function isPrimaryKey(): bool
    {
        return $this->foreignKey === null;
    }

    /**
     * @inheritDoc
     */
    public function getTypeIdentifier()
    {
        return PDO::PARAM_STR;
    }
}