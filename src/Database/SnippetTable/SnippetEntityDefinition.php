<?php

namespace spawnApp\Database\SnippetTable;


use DateTime;
use Exception;
use spawnApp\Services\SnippetManager;
use spawnCore\Database\Entity\Entity;
use spawnCore\Database\Entity\EntityTraits\EntityCreatedAtTrait;
use spawnCore\Database\Entity\EntityTraits\EntityIDTrait;
use spawnCore\Database\Entity\EntityTraits\EntityUpdatedAtTrait;

class SnippetEntityDefinition extends Entity
{
    use EntityIDTrait;
    use EntityUpdatedAtTrait;
    use EntityCreatedAtTrait;

    protected string $namespace;
    protected string $path;
    protected ?string $value;
    protected string $language_id;

    /**
     * @param string|array $definition
     */
    public function __construct(
        string $namespace,
        string $path,
        ?string $value,
        string $language_id,
        ?string $id = null,
        ?DateTime $createdAt = null,
        ?DateTime $updatedAt = null
    )
    {
        $this->setNamespace($namespace);
        $this->setPath($path);
        $this->setValue($value);
        $this->setLanguageId($language_id);
        $this->setId($id);
        $this->setUpdatedAt($updatedAt);
        $this->setCreatedAt($createdAt);
    }


    public function getRepositoryClass(): string
    {
        return SnippetRepository::class;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'namespace' => $this->getNamespace(),
            'path' => $this->getPath(),
            'value' => $this->getValue(),
            'languageId' => $this->getLanguageId(),
            'updatedAt' => $this->getUpdatedAt(),
            'createdAt' => $this->getCreatedAt(),
        ];
    }

    public static function getEntityFromArray(array $values): Entity
    {
        $values['updatedAt'] = self::getDateTimeFromVariable($values['updatedAt']??null);
        $values['createdAt'] = self::getDateTimeFromVariable($values['createdAt']??null);

        return new SnippetEntity(
            $values['namespace'],
            $values['path'],
            $values['value'],
            $values['languageId'],
            $values['id'] ?? null,
            $values['createdAt'] ?? null,
            $values['updatedAt'] ?? null
        );
    }

    public function getNamespace(): string
    {
        return $this->namespace;
    }

    public function setNamespace(string $namespace): self
    {
        $this->namespace = $namespace;
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

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): self
    {
        $this->value = $value;
        return $this;
    }

    public function getLanguageId(): string
    {
        return $this->language_id;
    }

    public function setLanguageId(string $language_id): self
    {
        $this->language_id = $language_id;
        return $this;
    }




}