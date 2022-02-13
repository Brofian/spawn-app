<?php

namespace SpawnCore\System\Database\Entity\EntityTraits;

Trait EntityIDTrait {

    protected ?string $id = null;

    public function getId(): ?string {
        return $this->id;
    }

    public function setId(?string $id): self {
        $this->id = $id;
        return $this;
    }

}