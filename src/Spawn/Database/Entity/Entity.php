<?php declare(strict_types=1);

namespace spawnCore\Database\Entity;


use spawnCore\Custom\Collection\AssociativeCollection;
use spawnCore\Custom\FoundationStorage\Mutable;

abstract class Entity extends Mutable
{

    protected ?string $id = null;

    protected ?AssociativeCollection $payload = null;

    public abstract function getRepositoryClass(): string;

    abstract public function toArray(): array;

    abstract public static function getEntityFromArray(array $values): Entity;

    public function getId(): ?string {
        return $this->id;
    }

    public function setId(?string $id): self {
        $this->id = $id;
        return $this;
    }

    public function getPayload(): AssociativeCollection {
        if(!$this->payload) {
            $this->payload = new AssociativeCollection();
        }
        return $this->payload;
    }


}