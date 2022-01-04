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

    /**
     * @param string|array $definition
     */
    public function __construct(
        string $short,
        ?string $parentId,
        ?string $id = null,
        ?DateTime $createdAt = null,
        ?DateTime $updatedAt = null
    )
    {
        $this->setShort($short);
        $this->setParentId($parentId);
        $this->setId($id);
        $this->setUpdatedAt($updatedAt);
        $this->setCreatedAt($createdAt);
    }


    public function getRepositoryClass(): string
    {
        return LanguageRepository::class;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'short' => $this->getShort(),
            'parentId' => $this->getParentId(),
            'updatedAt' => $this->getUpdatedAt(),
            'createdAt' => $this->getCreatedAt(),
        ];
    }

    public static function getEntityFromArray(array $values): Entity
    {
        $values['updatedAt'] = self::getDateTimeFromVariable($values['updatedAt']??null);
        $values['createdAt'] = self::getDateTimeFromVariable($values['createdAt']??null);

        return new LanguageEntity(
            $values['short'],
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


}