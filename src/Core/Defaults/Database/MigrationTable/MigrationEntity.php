<?php

namespace SpawnCore\Defaults\Database\MigrationTable;

use DateTime;
use SpawnCore\System\Database\Entity\Entity;
use SpawnCore\System\Database\Entity\EntityTraits\EntityCreatedAtTrait;
use SpawnCore\System\Database\Entity\EntityTraits\EntityIDTrait;
use SpawnCore\System\Database\Entity\EntityTraits\EntityUpdatedAtTrait;

class MigrationEntity extends Entity
{

    use EntityIDTrait;
    use EntityUpdatedAtTrait;
    use EntityCreatedAtTrait;

    protected string $class;
    protected int $timestamp;

    public function __construct(
        string $class,
        int $timestamp,
        ?string $id = null,
        ?DateTime $createdAt = null,
        ?DateTime $updatedAt = null
    )
    {
        $this->setClass($class);
        $this->setTimestamp($timestamp);
        $this->setId($id);
        $this->setUpdatedAt($updatedAt);
        $this->setCreatedAt($createdAt);
    }

    public function getRepositoryClass(): string
    {
        return MigrationRepository::class;
    }

    public static function getEntityFromArray(array $values): Entity
    {
        $values['updatedAt'] = self::getDateTimeFromVariable($values['updatedAt']??null);
        $values['createdAt'] = self::getDateTimeFromVariable($values['createdAt']??null);


        return new static(
            $values['class'],
            $values['timestamp'],
            $values['id'],
            $values['updatedAt'],
            $values['createdAt']
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'class' => $this->getClass(),
            'timestamp' => $this->getTimestamp(),
            'createdAt' => $this->getCreatedAt(),
            'updatedAt' => $this->getUpdatedAt(),
        ];
    }


    public function getClass(): string
    {
        return $this->class;
    }

    public function setClass(string $class): self
    {
        $this->class = $class;
        return $this;
    }

    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    public function setTimestamp(int $timestamp): self
    {
        $this->timestamp = $timestamp;
        return $this;
    }
}