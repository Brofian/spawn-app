<?php

namespace spawnApp\Database\ModuleTable;

use spawn\system\Core\Base\Database\Definition\Entity;

class ModuleEntity extends Entity {

    protected string $slug;
    protected string $path;
    protected bool $active;
    protected string $information;
    protected string $resourceConfig;
    protected ?\DateTime $createdAt;
    protected ?\DateTime $updatedAt;

    public function __construct(
        string $slug,
        string $path,
        bool $active,
        string $information,
        string $resourceConfig,
        ?string $id = null,
        ?\DateTime $createdAt = null,
        ?\DateTime $updatedAt = null)
    {
        $this->slug = $slug;
        $this->path = $path;
        $this->active = $active;
        $this->information = $information;
        $this->resourceConfig = $resourceConfig;
        $this->id = $id;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getRepositoryClass(): string
    {
        return ModuleRepository::class;
    }

    public static function getEntityFromArray(array $values): Entity
    {
        $createdAt = null;
        $updatedAt = null;
        try {
            $createdAt = new \DateTime($values['updatedAt']);
            $updatedAt = new \DateTime($values['updatedAt']);
        }
        catch (\Exception $e) {}

        return new static(
            $values['slug'],
            $values['path'],
            $values['active'],
            $values['information'],
            $values['resourceConfig'],
            $values['id'],
            $updatedAt,
            $createdAt
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'slug' => $this->getSlug(),
            'path' => $this->getPath(),
            'information' => $this->getInformation(),
            'resourceConfig' => $this->getResourceConfig(),
            'createdAt' => $this->getCreatedAt(),
            'updatedAt' => $this->getUpdatedAt(),
        ];
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): ModuleEntity
    {
        $this->slug = $slug;
        return $this;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): ModuleEntity
    {
        $this->path = $path;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): ModuleEntity
    {
        $this->active = $active;
        return $this;
    }

    public function getInformation(): string
    {
        return $this->information;
    }

    public function setInformation(string $information): ModuleEntity
    {
        $this->information = $information;
        return $this;
    }

    public function getResourceConfig(): string
    {
        return $this->resourceConfig;
    }

    public function setResourceConfig(string $resourceConfig): ModuleEntity
    {
        $this->resourceConfig = $resourceConfig;
        return $this;
    }





    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }


}