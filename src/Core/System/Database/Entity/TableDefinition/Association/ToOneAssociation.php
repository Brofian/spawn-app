<?php

namespace SpawnCore\System\Database\Entity\TableDefinition\Association;

use SpawnCore\System\Custom\Gadgets\Slugifier;
use SpawnCore\System\Custom\Gadgets\UUID;
use SpawnCore\System\Database\Criteria\Criteria;
use SpawnCore\System\Database\Criteria\Filters\EqualsFilter;
use SpawnCore\System\Database\Entity\EntityCollection;

class ToOneAssociation extends AbstractAssociation {

    public function applyAssociation(EntityCollection $collection, ?string $associationChain): void
    {
        $entityValueGetter = 'get'.ucfirst($this->getThisColumn());
        $entityValueSetter = 'set'.Slugifier::toPascalCase($this->getOtherEntity());

        foreach($collection as $entity) {
            $entityIdentifierValue = $entity->{$entityValueGetter}();
            if(!$this->isPreventUuidConversion()) {
                $entityIdentifierValue = UUID::hexToBytes($entityIdentifierValue);
            }

            $criteria = new Criteria(
                new EqualsFilter($this->getOtherColumn(), $entityIdentifierValue)
            );

            if($associationChain) {
                $criteria->addAssociation($associationChain);
            }

            $childEntity = $this->getOtherRepository()->search($criteria, 1)->first();
            $entity->{$entityValueSetter}($childEntity);
        }
    }
}