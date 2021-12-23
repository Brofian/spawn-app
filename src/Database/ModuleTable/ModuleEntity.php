<?php

namespace spawnApp\Database\ModuleTable;

use DateTime;
use Exception;
use spawnCore\Database\Entity\Entity;

class ModuleEntity extends Entity {

    protected string $slug;
    protected string $path;
    protected bool $active;
    protected array $information;
    protected array $resourceConfig;
    protected ?DateTime $createdAt;
    protected ?DateTime $updatedAt;

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
        $this->slug = $slug;
        $this->path = $path;
        $this->active = $active;
        if(is_array($information)) $this->information = $information;
        elseif(is_string($information)) $this->information = json_decode($information, true);

        if(is_array($resourceConfig)) $this->resourceConfig = $resourceConfig;
        elseif(is_string($resourceConfig)) $this->resourceConfig = json_decode($resourceConfig, true);

        $this->id = $id;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getRepositoryClass(): string
    {
        return ModuleRepository::class;
    }

    public static function getEntityFromArray(array $values): ModuleEntity
    {
        $createdAt = null;
        $updatedAt = null;
        try {
            if(isset($values['createdAt'])) $createdAt = new DateTime($values['createdAt']);
            if(isset($values['updatedAt'])) $updatedAt = new DateTime($values['updatedAt']);
        }
        catch (Exception $e) {}

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

    public function setInformation(array $information): ModuleEntity
    {
        $this->information = $information;
        return $this;
    }

    public function getResourceConfig(bool $asArray = false): array
    {
        return $this->resourceConfig;
    }

    public function setResourceConfig(array $resourceConfig): ModuleEntity
    {
        $this->resourceConfig = $resourceConfig;
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

    public static function sortModuleEntityArrayByWeight(array $moduleEntities): array {
        usort($moduleEntities, function($a, $b) {
            /** @var $a ModuleEntity */
            /** @var $b ModuleEntity */
            $aWeight = $a->getResourceConfigValue('weight', 0);
            $bWeight = $b->getResourceConfigValue('weight', 0);

            if($aWeight < $bWeight) return -1;
            else if($aWeight > $bWeight) return 1;
            else return 0;
        });

        return $moduleEntities;
    }



    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getNamespace(): string {
        return $this->getResourceConfigValue('namespace', $this->getSlug());
    }

}