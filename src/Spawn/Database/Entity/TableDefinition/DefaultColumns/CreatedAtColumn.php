<?php

namespace spawn\Core\Base\Database\Definition\TableDefinition\DefaultColumns;


use spawnCore\Database\Entity\TableDefinition\AbstractColumn;
use spawnCore\Database\Entity\TableDefinition\Constants\ColumnTypes;

class CreatedAtColumn extends AbstractColumn {


    public function getName(): string
    {
        return 'createdAt';
    }

    public function getType(): string
    {
        return ColumnTypes::DATETIME_TZ;
    }

    public function canBeNull(): ?bool
    {
        return false;
    }


    public function getTypeIdentifier()
    {
        return 'datetime'; //php´s \DateTime()
    }
}