<?php

namespace spawnApp\Database\ConfigurationTable;


use DateTime;
use Exception;
use spawnCore\Database\Entity\Entity;
use spawnCore\Database\Entity\EntityTraits\EntityCreatedAtTrait;
use spawnCore\Database\Entity\EntityTraits\EntityIDTrait;
use spawnCore\Database\Entity\EntityTraits\EntityUpdatedAtTrait;

class ConfigurationEntityDefinition extends Entity
{
    use EntityIDTrait;
    use EntityUpdatedAtTrait;
    use EntityCreatedAtTrait;

    protected string $internalName;
    protected string $type;
    protected array $definition;
    protected string $folder;

    public function __construct(
        string $internalName,
        string $type,
        $definition,
        string $folder,
        ?string $id = null,
        ?DateTime $createdAt = null,
        ?DateTime $updatedAt = null
    )
    {
        $this->internalName = $internalName;
        $this->type = $type;
        $this->definition = $definition;
        $this->folder = $folder;
        $this->id = $id;
        $this->updatedAt = $updatedAt;
        $this->createdAt = $createdAt;
    }


    public function getRepositoryClass(): string
    {
        return ConfigurationRepository::class;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'internalName' => $this->getInternalName(),
            'type' => $this->getType(),
            'definition' => $this->getDefinition(),
            'folder' => $this->getFolder(),
            'updatedAt' => $this->getUpdatedAt(),
            'createdAt' => $this->getCreatedAt(),
        ];
    }

    public static function getEntityFromArray(array $values): Entity
    {
        $values['updatedAt'] = self::getDateTimeFromVariable($values['updatedAt']);
        $values['createdAt'] = self::getDateTimeFromVariable($values['createdAt']);

        return new ConfigurationEntity(
            $values['internalName'],
            $values['type'],
            $values['definition'],
            $values['folder'],
            $values['id'] ?? null,
            $values['createdAt'] ?? null,
            $values['updatedAt'] ?? null
        );
    }

    public function getInternalName(): string
    {
        return $this->internalName;
    }

    public function setInternalName(string $internalName): self
    {
        $this->internalName = $internalName;
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getDefinition(): array
    {
        return $this->definition;
    }

    public function setDefinition(array $definition): self
    {
        $this->definition = $definition;
        return $this;
    }

    public function getFolder(): string
    {
        return $this->folder;
    }

    public function setFolder(string $folder): self
    {
        $this->folder = $folder;
        return $this;
    }

}