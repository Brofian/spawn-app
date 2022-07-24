<?php

namespace SpawnCore\System\Database\Entity\TableDefinition\Association;

use SpawnCore\System\Database\Entity\EntityCollection;
use SpawnCore\System\Database\Entity\TableRepository;
use SpawnCore\System\ServiceSystem\ServiceContainerProvider;

abstract class AbstractAssociation {

    protected string $thisColumn;
    protected string $otherEntity;
    protected string $otherColumn;
    protected bool $preventUuidConversion;
    protected ?TableRepository $otherRepository = null;

    public function __construct(string $thisColumn, string $otherEntity, string $otherColumn, bool $preventUuidConversion = false)
    {
        $this->thisColumn = $thisColumn;
        $this->otherEntity = $otherEntity;
        $this->otherColumn = $otherColumn;
        $this->preventUuidConversion = $preventUuidConversion;
    }

    public function getThisColumn(): string
    {
        return $this->thisColumn;
    }

    public function getOtherEntity(): string
    {
        return $this->otherEntity;
    }

    public function getOtherColumn(): string
    {
        return $this->otherColumn;
    }

    public function isPreventUuidConversion(): bool
    {
        return $this->preventUuidConversion;
    }

    public function getOtherRepository(): TableRepository {
        if($this->otherRepository instanceof TableRepository) {
            return $this->otherRepository;
        }

        /** @var TableRepository|null $otherRepository */
        $this->otherRepository = ServiceContainerProvider::getServiceContainer()->get($this->getOtherEntity().'.repository');

        if(!$this->otherRepository instanceof TableRepository) {
            throw new InvalidAssociationException($this->otherEntity);
        }

        return $this->otherRepository;
    }

    abstract public function applyAssociation(EntityCollection $collection, string $associationChain): void;



}