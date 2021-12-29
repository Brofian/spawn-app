<?php

namespace spawnCore\Database\Entity\EntityTraits;

use DateTime;

Trait EntityUpdatedAtTrait {

    protected ?DateTime $updatedAt = null;
    
    public function getUpdatedAt(): ?DateTime {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?DateTime $updatedAt): self {
        $this->updatedAt = $updatedAt;
        return $this;
    }

}