<?php declare(strict_types=1);

namespace SpawnCore\System\Database\Entity;

use SpawnCore\System\Custom\Collection\Collection;

class EntityCollection extends Collection {

    protected string $containedEntityType;

    public function __construct(string $containedEntityType)
    {
        $this->containedEntityType = $containedEntityType;
    }

    public function getContainedEntityType(): string {
        return $this->containedEntityType;
    }

    public function add($value): void {
        if($value instanceof $this->containedEntityType) {
            $this->collection[] = $value;
            $this->count = null;
        }
    }

    public function addBulk(...$values): void {
        foreach($values as $value) {
            if($value instanceof $this->containedEntityType) {
                $this->collection[] = $value;
                $this->count = null;
            }
        }
    }
    
    public function getCount(): int {
        return $this->count();
    }

}