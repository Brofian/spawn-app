<?php declare(strict_types=1);

namespace spawnCore\Database\Entity;

use spawnCore\Custom\Collection\Collection;

class EntityCollection extends Collection {

    protected string $containedEntityType;

    public function __construct(string $containedEntityType)
    {
        $this->containedEntityType = $containedEntityType;
    }

    public function getContainedEntityType(): string {
        return $this->containedEntityType;
    }

    public function add($value) {
        if($value instanceof $this->containedEntityType) {
            $this->collection[] = $value;
        }
    }

    public function addBulk(...$values) {
        foreach($values as $value) {
            if($value instanceof $this->containedEntityType) {
                $this->collection[] = $value;
            }
        }
    }


}