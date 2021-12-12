<?php

namespace spawnApp\Database\CronTable;


use DateTime;
use Exception;
use spawnCore\Database\Entity\Entity;
use spawnCore\Cron\CronStates;

class CronEntityDefinition extends Entity
{
    protected string $action;
    protected string $result;
    protected string $state;
    protected ?DateTime $createdAt;
    protected ?DateTime $updatedAt;

    public function __construct(
        string $action,
        string $result,
        string $state = CronStates::DEFAULT_STATE,
        ?string $id = null,
        ?DateTime $createdAt = null,
        ?DateTime $updatedAt = null
    )
    {
        $this->action = $action;
        $this->result = $result;
        $this->state = $state;
        $this->id = $id;
        $this->updatedAt = $updatedAt;
        $this->createdAt = $createdAt;
    }


    public function getRepositoryClass(): string
    {
        return CronRepository::class;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'action' => $this->getAction(),
            'result' => $this->getResult(),
            'state' => $this->getState(),
            'updatedAt' => $this->getUpdatedAt(),
            'createdAt' => $this->getCreatedAt(),
        ];
    }

    public static function getEntityFromArray(array $values): Entity
    {
        if(!$values['updatedAt'] instanceof DateTime) {
            try {   $values['updatedAt'] = new DateTime($values['updatedAt']); }
            catch (Exception $e) { $values['updatedAt'] = new DateTime(); }
        }

        if(!$values['createdAt'] instanceof DateTime) {
            try {   $values['createdAt'] = new DateTime($values['createdAt']); }
            catch (Exception $e) { $values['createdAt'] = new DateTime(); }
        }

        return new CronEntity(
            $values['action'],
            $values['result'],
            $values['state'],
            $values['id'] ?? null,
            $values['createdAt'] ?? null,
            $values['updatedAt'] ?? null
        );
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function setAction(string $action): void
    {
        $this->action = $action;
    }

    public function getResult(): string
    {
        return $this->result;
    }

    public function setResult(string $result): void
    {
        $this->result = $result;
    }


    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function setState(string $state): void
    {
        $this->state = $state;
    }



}