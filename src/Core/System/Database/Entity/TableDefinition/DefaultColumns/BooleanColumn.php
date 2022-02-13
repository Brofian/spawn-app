<?php

namespace SpawnCore\System\Database\Entity\TableDefinition\DefaultColumns;


use PDO;
use SpawnCore\System\Database\Entity\TableDefinition\AbstractColumn;
use SpawnCore\System\Database\Entity\TableDefinition\Constants\ColumnTypes;

class BooleanColumn extends AbstractColumn {

    protected string $columnName;
    protected bool $default;

    public function __construct(
        string $columnName,
        bool $default = false
    )
    {
        $this->columnName = $columnName;
        $this->default = $default;
    }


    public function getName(): string
    {
        return $this->columnName;
    }

    public function getType(): string
    {
        return ColumnTypes::BOOLEAN;
    }

    public function canBeNull(): ?bool
    {
        return true;
    }

    public function getDefault()
    {
        return ($this->default) ? 1 : 0;
    }

    public function getTypeIdentifier()
    {
        return PDO::PARAM_BOOL;
    }
}