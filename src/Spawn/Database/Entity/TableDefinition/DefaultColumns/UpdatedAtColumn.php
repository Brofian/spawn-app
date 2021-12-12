<?php

namespace spawnCore\Database\Entity\TableDefinition\DefaultColumns;


use spawnCore\Database\Entity\TableDefinition\AbstractColumn;
use spawnCore\Database\Entity\TableDefinition\Constants\ColumnTypes;

class UpdatedAtColumn extends AbstractColumn {


    public function getName(): string
    {
        return 'updatedAt';
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