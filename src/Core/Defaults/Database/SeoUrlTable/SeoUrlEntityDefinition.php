<?php declare(strict_types = 1);
namespace SpawnCore\Defaults\Database\SeoUrlTable;

use DateTime;
use SpawnCore\System\Database\Entity\Entity;
use SpawnCore\System\Database\Entity\EntityTraits\EntityCreatedAtTrait;
use SpawnCore\System\Database\Entity\EntityTraits\EntityIDTrait;
use SpawnCore\System\Database\Entity\EntityTraits\EntityUpdatedAtTrait;

class SeoUrlEntityDefinition extends Entity
{
    use EntityIDTrait;
    use EntityUpdatedAtTrait;
    use EntityCreatedAtTrait;

    protected ?string $name;
    protected string $cUrl;
    protected string $controller;
    protected string $action;
    protected array $parameters;
    protected bool $locked;
    protected bool $active;
    protected bool $requiresAdmin;
    protected bool $requiresUser;
    protected bool $api;

    public function __construct(
        ?string $name,
        string $cUrl,
        string $controller,
        string $action,
        array $parameters,
        bool $locked = false,
        bool $active = true,
        bool $requiresAdmin = false,
        bool $requiresUser = false,
        bool $api = false,
        ?string $id = null,
        ?DateTime $createdAt = null,
        ?DateTime $updatedAt = null)
    {
        $this->setName($name);
        $this->setCUrl($cUrl);
        $this->setController($controller);
        $this->setAction($action);
        $this->setParameters($parameters);
        $this->setLocked($locked);
        $this->setActive($active);
        $this->setRequiresAdmin($requiresAdmin);
        $this->setRequiresUser($requiresUser);
        $this->setApi($api);
        $this->setId($id);
        $this->setCreatedAt($createdAt);
        $this->setUpdatedAt($updatedAt);
    }

    public function getRepositoryClass(): string
    {
        return SeoUrlRepository::class;
    }

    public static function getEntityFromArray(array $values): self
    {
        $values['updatedAt'] = self::getDateTimeFromVariable($values['updatedAt']??null);
        $values['createdAt'] = self::getDateTimeFromVariable($values['createdAt']??null);
        $values['parameters'] = self::getArrayFromVariable($values['parameters']??null);

        return new SeoUrlEntity(
            $values['name'],
            $values['cUrl'],
            $values['controller'],
            $values['action'],
            $values['parameters'],
            (bool)$values['locked'],
            (bool)$values['active'],
            (bool)$values['requiresAdmin'],
            (bool)$values['requiresUser'],
            (bool)$values['api'],
            $values['id'],
            $values['updatedAt'],
            $values['createdAt']
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'cUrl' => $this->getCUrl(),
            'controller' => $this->getController(),
            'action' => $this->getAction(),
            'parameters' => $this->getParameters(),
            'locked' => $this->isLocked(),
            'active' => $this->isActive(),
            'requiresAdmin' => $this->isRequiresAdmin(),
            'requiresUser' => $this->isRequiresUser(),
            'api' => $this->isApi(),
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

    public function isRequiresAdmin(): bool
    {
        return $this->requiresAdmin;
    }

    public function setRequiresAdmin(bool $requiresAdmin): self
    {
        $this->requiresAdmin = $requiresAdmin;
        return $this;
    }

    public function isRequiresUser(): bool
    {
        return $this->requiresUser;
    }

    public function setRequiresUser(bool $requiresUser): self
    {
        $this->requiresUser = $requiresUser;
        return $this;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function getJsonParameters(): string
    {
        return json_encode($this->parameters, JSON_THROW_ON_ERROR);
    }

    public function setParameters(array $parameters): void
    {
        $this->parameters = $parameters;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function isApi(): bool
    {
        return $this->api;
    }

    public function setApi(bool $api): self
    {
        $this->api = $api;
        return $this;
    }


}