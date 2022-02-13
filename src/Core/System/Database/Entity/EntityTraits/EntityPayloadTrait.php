<?php

namespace SpawnCore\System\Database\Entity\EntityTraits;

use SpawnCore\System\Custom\Collection\AssociativeCollection;

Trait EntityPayloadTrait {

    protected ?AssociativeCollection $payload = null;

    public function getPayload(): AssociativeCollection {
        if(!$this->payload) {
            $this->payload = new AssociativeCollection();
        }
        return $this->payload;
    }

}