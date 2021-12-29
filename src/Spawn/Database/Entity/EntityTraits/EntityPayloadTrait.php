<?php

namespace spawnCore\Database\Entity\EntityTraits;

use spawnCore\Custom\Collection\AssociativeCollection;

Trait EntityPayloadTrait {

    protected ?AssociativeCollection $payload = null;

    public function getPayload(): AssociativeCollection {
        if(!$this->payload) {
            $this->payload = new AssociativeCollection();
        }
        return $this->payload;
    }

}