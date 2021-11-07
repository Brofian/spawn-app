<?php

namespace spawnApp\Database\SeoUrlTable;

use Doctrine\DBAL\Exception;
use spawn\system\Core\Base\Database\Definition\Entity;

class SeoUrlEntityDefinition extends Entity {

    protected string $cUrl;
    protected string $controller;
    protected string $action;
    protected array $parameters;
    protected bool $locked;
    protected bool $active;
    protected ?\DateTime $createdAt;
    protected ?\DateTime $updatedAt;

    public function __construct(
        string $cUrl,
        string $controller,
        string $action,
        array $parameters,
        bool $locked = false,
        bool $active = true,
        ?string $id = null,
        ?\DateTime $createdAt = null,
        ?\DateTime $updatedAt = null)
    {
        $this->cUrl = $cUrl;
        $this->controller = $controller;
        $this->action = $action;
        $this->parameters = $parameters;
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
        if(!$values['updatedAt'] instanceof \DateTime) {
            try {
                $values['updatedAt'] = new \DateTime($values['updatedAt']);
            }
            catch (\Exception $e) {
                $values['updatedAt'] = new \DateTime();
            }
        }

        if(!$values['createdAt'] instanceof \DateTime) {
            try {
                $values['createdAt'] = new \DateTime($values['createdAt']);
            }
            catch (\Exception $e) {
                $values['createdAt'] = new \DateTime();
            }
        }

        if(!isset($values['parameters'])) {
            $values['parameters'] = [];
        }
        elseif(!is_array($values['parameters']))
        {
            try {
                $values['parameters'] = json_decode($values['parameters']);
            }
            catch (\Exception $e) {
                $values['parameters'] = [];
            }
        }



        return new static(
            $values['cUrl'],
            $values['controller'],
            $values['action'],
            $values['parameters'],
            $values['locked'],
            $values['active'],
            $values['id'],
            $values['updatedAt'],
            $values['createdAt']
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'cUrl' => $this->getCUrl(),
            'controller' => $this->getController(),
            'action' => $this->getAction(),
            'parameters' => $this->getParameters(),
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

    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function getJsonParameters(): string
    {
        return json_encode($this->parameters);
    }

    public function setParameters(array $parameters): void
    {
        $this->parameters = $parameters;
    }




}