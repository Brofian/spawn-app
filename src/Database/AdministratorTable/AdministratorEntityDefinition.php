<?php

namespace spawnApp\Database\AdministratorTable;


use spawn\system\Core\Base\Database\Definition\Entity;

class AdministratorEntityDefinition extends Entity
{
    protected string $username;
    protected string $password;
    protected string $email;
    protected bool $active;
    protected ?\DateTime $createdAt;
    protected ?\DateTime $updatedAt;

    public function __construct(
        string $username,
        string $password,
        string $email,
        bool $active = true,
        ?string $id = null,
        ?\DateTime $createdAt = null,
        ?\DateTime $updatedAt = null
    )
    {
        $this->username = $username;
        $this->password = $password;
        $this->email = $email;
        $this->active = $active;
        $this->id = $id;
        $this->updatedAt = $updatedAt;
        $this->createdAt = $createdAt;
    }


    public function getRepositoryClass(): string
    {
        return AdministratorRepository::class;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'username' => $this->getUsername(),
            'password' => $this->getPassword(),
            'email' => $this->getEmail(),
            'active' => $this->isActive(),
            'updatedAt' => $this->getUpdatedAt(),
            'createdAt' => $this->getCreatedAt()
        ];
    }

    public static function getEntityFromArray(array $values): Entity
    {
        if(!$values['updatedAt'] instanceof \DateTime) {
            try {   $values['updatedAt'] = new \DateTime($values['updatedAt']); }
            catch (\Exception $e) { $values['updatedAt'] = new \DateTime(); }
        }

        if(!$values['createdAt'] instanceof \DateTime) {
            try {   $values['createdAt'] = new \DateTime($values['createdAt']); }
            catch (\Exception $e) { $values['createdAt'] = new \DateTime(); }
        }


        return new AdministratorEntity(
            $values['username'],
            $values['password'],
            $values['email'],
            $values['active'],
            $values['id'] ?? null,
            $values['createdAt'] ?? null,
            $values['updatedAt'] ?? null
        );
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }





}