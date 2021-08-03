<?php

namespace spawnApp\Database\SeoUrlTable;

use spawn\system\Core\Base\Database\Definition\Entity;

class SeoUrlEntityDefinition extends Entity {

    protected string $cUrl;
    protected string $controller;
    protected string $action;
    protected bool $locked;
    protected bool $active;
    protected ?\DateTime $createdAt;
    protected ?\DateTime $updatedAt;

    public function __construct(
        string $cUrl,
        string $controller,
        string $action,
        bool $locked = false,
        bool $active = true,
        ?string $id = null,
        ?\DateTime $createdAt = null,
        ?\DateTime $updatedAt = null)
    {
        $this->cUrl = $cUrl;
        $this->controller = $controller;
        $this->action = $action;
        $this->locked = $locked;
        $this->active = $active;
        $this->id = $id;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getRepositoryClass(): string
    {
        return SeoUrlRepository::class;
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
            $values['cUrl'],
            $values['controller'],
            $values['action'],
            $values['locked'],
            $values['active'],
            $values['id'],
            $updatedAt,
            $createdAt
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'cUrl' => $this->getCUrl(),
            'controller' => $this->getController(),
            'action' => $this->getAction(),
            'locked' => $this->isLocked(),
            'active' => $this->isActive(),
            'createdAt' => $this->getCreatedAt(),
            'updatedAt' => $this->getUpdatedAt(),
        ];
    }

    public function getCUrl(): string
    {
        return $this->cUrl;
    }

    public function setCUrl(string $cUrl): self
    {
        $this->cUrl = $cUrl;
        return $this;
    }

    public function getController(): string
    {
        return $this->controller;
    }

    public function setController(string $controller): self
    {
        $this->controller = $controller;
        return $this;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function setAction(string $action): self
    {
        $this->action = $action;
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

    public function isLocked(): bool
    {
        return $this->locked;
    }

    public function setLocked(bool $locked): self
    {
        $this->locked = $locked;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;
        return $this;
    }




}