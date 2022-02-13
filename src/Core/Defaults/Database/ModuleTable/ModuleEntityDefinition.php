<?php

namespace SpawnCore\Defaults\Database\ModuleTable;

use DateTime;
use SpawnCore\System\Database\Entity\Entity;
use SpawnCore\System\Database\Entity\EntityTraits\EntityCreatedAtTrait;
use SpawnCore\System\Database\Entity\EntityTraits\EntityIDTrait;
use SpawnCore\System\Database\Entity\EntityTraits\EntityUpdatedAtTrait;

class ModuleEntityDefinition extends Entity {

    use EntityIDTrait;
    use EntityUpdatedAtTrait;
    use EntityCreatedAtTrait;

    protected string $slug;
    protected string $path;
    protected bool $active;
    protected array $information;
    protected array $resourceConfig;

    public function __construct(
        string $slug,
        string $path,
        bool $active,
        $information,
        $resourceConfig,
        ?string $id = null,
        ?DateTime $createdAt = null,
        ?DateTime $updatedAt = null
    )
    {
        $this->setSlug($slug);
        $this->setPath($path);
        $this->setActive($active);
        $this->setInformation($information);
        $this->setResourceConfig($resourceConfig);
        $this->setId($id);
        $this->setUpdatedAt($updatedAt);
        $this->setCreatedAt($createdAt);
    }

    public function getRepositoryClass(): string
    {
        return ModuleRepository::class;
    }

    public static function getEntityFromArray(array $values): self
    {
        $values['createdAt'] = self::getDateTimeFromVariable($values['createdAt']??null);
        $values['updatedAt'] = self::getDateTimeFromVariable($values['updatedAt']??null);

        return new static(
            $values['slug'],
            $values['path'],
            $values['active'],
            $values['information'],
            $values['resourceConfig'],
            $values['id'],
            $values['createdAt'],
            $values['updatedAt']
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'slug' => $this->getSlug(),
            'path' => $this->getPath(),
            'information' => json_encode($this->getInformations()),
            'resourceConfig' => json_encode($this->getResourceConfig()),
            'createdAt' => $this->getCreatedAt(),
            'updatedAt' => $this->getUpdatedAt(),
        ];
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;
        return $this;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;
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

    public function getInformations(): array
    {
        return $this->information;
    }

    /**
     * @return mixed
     */
    public function getInformation(string $key, $default = null)
    {
        if(isset($this->information[$key])) {
            return $this->information[$key];
        }
        return $default;
    }

    /**
     * @param string|array $information
     */
    public function setInformation($information): self
    {
        if(is_array($information)) {
            $this->information = $information;
        }
        else {
            $this->information = json_decode($information, true);
        }


        return $this;
    }

    public function getResourceConfig(bool $asArray = false): array
    {
        return $this->resourceConfig;
    }

    /**
     * @param string|array $resourceConfig
     */
    public function setResourceConfig($resourceConfig): self
    {
        if(is_array($resourceConfig)) {
            $this->resourceConfig = $resourceConfig;
        }
        else {
            $this->resourceConfig = json_decode($resourceConfig, true);
        }

        return $this;
    }

    /**
     * @return mixed;
     */
    public function getResourceConfigValue(string $key, $default = null) {
        if(isset($this->resourceConfig[$key])) {
            return $this->resourceConfig[$key];
        }
        return $default;
    }


    public function getNamespace(): string {
        return $this->getResourceConfigValue('namespace', $this->getSlug());
    }

}