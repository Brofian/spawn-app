<?php

namespace SpawnCore\System\Database\Entity\TableDefinition\DefaultColumns;


use SpawnCore\System\Database\Entity\TableDefinition\AbstractColumn;
use SpawnCore\System\Database\Entity\TableDefinition\Constants\ColumnTypes;

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