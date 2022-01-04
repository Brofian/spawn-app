<?php

namespace spawnApp\Database\LanguageTable;


use DateTime;
use Exception;
use spawnCore\Database\Entity\Entity;
use spawnCore\Database\Entity\EntityTraits\EntityCreatedAtTrait;
use spawnCore\Database\Entity\EntityTraits\EntityIDTrait;
use spawnCore\Database\Entity\EntityTraits\EntityUpdatedAtTrait;

class LanguageEntityDefinition extends Entity
{
    use EntityIDTrait;
    use EntityUpdatedAtTrait;
    use EntityCreatedAtTrait;

    protected string $short;
    protected ?string $parentId;
    protected ?string $value;

    /**
     * @param string|array $definition
     */
    public function __construct(
        string $short,
        ?string $value,
        ?string $parentId,
        ?string $id = null,
        ?DateTime $createdAt = null,
        ?DateTime $updatedAt = null
    )
    {
        $this->setShort($short);
        $this->setValue($value);
        $this->setParentId($parentId);
        $this->setId($id);
        $this->setUpdatedAt($updatedAt);
        $this->setCreatedAt($createdAt);
    }


    public function getRepositoryClass(): string
    {
        return ConfigurationRepository::class;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'short' => $this->getShort(),
            'parentId' => $this->getParentId(),
            'value' => $this->getValue(),
            'updatedAt' => $this->getUpdatedAt(),
            'createdAt' => $this->getCreatedAt(),
        ];
    }

    public static function getEntityFromArray(array $values): Entity
    {
        $values['updatedAt'] = self::getDateTimeFromVariable($values['updatedAt']??null);
        $values['createdAt'] = self::getDateTimeFromVariable($values['createdAt']??null);

        return new ConfigurationEntity(
            $values['short'],
            $values['value'],
            $values['parentId'],
            $values['id'] ?? null,
            $values['createdAt'] ?? null,
            $values['updatedAt'] ?? null
        );
    }

    public function getShort(): string
    {
        return $this->short;
    }

    public function setShort(string $short): self
    {
        $this->short = $short;
        return $this;
    }

    public function getParentId(): ?string
    {
        return $this->parentId;
    }

    public function setParentId(?string $parentId): self
    {
        $this->parentId = $parentId;
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



}