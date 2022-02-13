<?php

namespace SpawnCore\Defaults\Database\CronTable;


use DateTime;
use SpawnCore\System\Cron\CronStates;
use SpawnCore\System\Database\Entity\Entity;
use SpawnCore\System\Database\Entity\EntityTraits\EntityCreatedAtTrait;
use SpawnCore\System\Database\Entity\EntityTraits\EntityIDTrait;
use SpawnCore\System\Database\Entity\EntityTraits\EntityUpdatedAtTrait;

class CronEntityDefinition extends Entity
{
    use EntityIDTrait;
    use EntityUpdatedAtTrait;
    use EntityCreatedAtTrait;


    protected string $action;
    protected string $result;
    protected string $state;

    public function __construct(
        string $action,
        string $result,
        string $state = CronStates::DEFAULT_STATE,
        ?string $id = null,
        ?DateTime $createdAt = null,
        ?DateTime $updatedAt = null
    )
    {
        $this->setAction($action);
        $this->setResult($result);
        $this->setState($state);
        $this->setId($id);
        $this->setUpdatedAt($updatedAt);
        $this->setId($id);
        $this->setUpdatedAt($updatedAt);
        $this->setCreatedAt($createdAt);
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
        $values['updatedAt'] = self::getDateTimeFromVariable($values['updatedAt']??null);
        $values['createdAt'] = self::getDateTimeFromVariable($values['createdAt']??null);

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

    public function getState(): string
    {
        return $this->state;
    }

    public function setState(string $state): void
    {
        $this->state = $state;
    }



}