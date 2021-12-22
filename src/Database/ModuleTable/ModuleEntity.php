<?php

namespace spawnApp\Database\ModuleTable;

use DateTime;
use Exception;
use spawnCore\Database\Entity\Entity;

class ModuleEntity extends Entity {

    protected string $slug;
    protected string $path;
    protected bool $active;
    protected string $information;
    protected string $resourceConfig;
    protected array $decodedConfig = [];
    protected ?DateTime $createdAt;
    protected ?DateTime $updatedAt;

    public function __construct(
        string $slug,
        string $path,
        bool $active,
        string $information,
        string $resourceConfig,
        ?string $id = null,
        ?DateTime $createdAt = null,
        ?DateTime $updatedAt = null
    )
    {
        $this->slug = $slug;
        $this->path = $path;
        $this->active = $active;
        $this->information = $information;
        $this->resourceConfig = $resourceConfig;
        if($resourceConfig) {
            $this->decodedConfig = json_decode($resourceConfig, true);
        }
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

    public function getInformationValue(string $key, $default = ''): string {
        $data = json_decode($this->getInformation(), true, 999, JSON_THROW_ON_ERROR);
        if(isset($data[$key])) {
            return $data[$key];
        }
        return $default;
    }

    public function setInformation(string $information): ModuleEntity
    {
        $this->information = $information;
        return $this;
    }

    /**
     * @param bool $asArray
     * @return string|array
     */
    public function getResourceConfig(bool $asArray = false)
    {
        if($asArray) {
            return $this->decodedConfig;
        }

        return $this->resourceConfig;
    }

    public function setResourceConfig(string $resourceConfig): ModuleEntity
    {
        $this->resourceConfig = $resourceConfig;
        return $this;
    }

    /**
     * @return mixed;
     */
    public function getResourceConfigValue(string $key, $default = null) {
        if(isset($this->decodedConfig[$key])) {
            return $this->decodedConfig[$key];
        }
        return $default;
    }

    /**
     * @param ModuleEntity[] $moduleEntities
     * @return array
     */
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


}