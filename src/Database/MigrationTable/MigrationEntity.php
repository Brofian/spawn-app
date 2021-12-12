<?php

namespace spawnApp\Database\MigrationTable;

use spawnCore\Database\Entity\Entity;

class MigrationEntity extends Entity {

    protected string $class;
    protected int $timestamp;
    protected ?\DateTime $createdAt;
    protected ?\DateTime $updatedAt;

    public function __construct(string $class, int $timestamp, ?string $id = null, ?\DateTime $createdAt = null, ?\DateTime $updatedAt = null)
    {
        $this->class = $class;
        $this->timestamp = $timestamp;
        $this->id = $id;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getRepositoryClass(): string
    {
        return MigrationRepository::class;
    }

    public static function getEntityFromArray(array $values): Entity
    {
        $createdAt = null;
        $updatedAt = null;
        try {
            $createdAt = new \DateTime($values['updatedAt']);
            $updatedAt = new \DateTime($values['updatedAt']);
        }
        catch (\Exception $e) {}

        return new static(
            $values['class'],
            $values['timestamp'],
            $values['id'],
            $updatedAt,
            $createdAt
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

    public function setClass(string $class): MigrationEntity
    {
        $this->class = $class;
        return $this;
    }

    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    public function setTimestamp(int $timestamp): MigrationEntity
    {
        $this->timestamp = $timestamp;
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


}