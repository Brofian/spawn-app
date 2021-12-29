<?php

namespace spawnCore\Database\Entity\EntityTraits;

use DateTime;

Trait EntityCreatedAtTrait {

    protected ?DateTime $createdAt = null;
    
    public function getCreatedAt(): ?DateTime {
        return $this->createdAt;
    }

    public function setCreatedAt(?DateTime $createdAt): self {
        $this->createdAt = $createdAt;
        return $this;
    }

}