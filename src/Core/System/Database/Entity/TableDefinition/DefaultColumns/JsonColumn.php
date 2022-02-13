<?php

namespace SpawnCore\System\Database\Entity\TableDefinition\DefaultColumns;


use PDO;
use SpawnCore\System\Database\Entity\TableDefinition\AbstractColumn;
use SpawnCore\System\Database\Entity\TableDefinition\Constants\ColumnTypes;

class JsonColumn extends AbstractColumn {

    protected string $columnName;
    protected bool $canBeNull;

    public function __construct(
        string $columnName,
        bool $canBeNull = true
    )
    {
        $this->columnName = $columnName;
        $this->canBeNull = $canBeNull;
    }


    public function getName(): string
    {
        return $this->columnName;
    }

    public function getType(): string
    {
        return ColumnTypes::JSON;
    }

    public function canBeNull(): ?bool
    {
        return $this->canBeNull;
    }


    public function getTypeIdentifier()
    {
        return PDO::PARAM_STR;
    }
}