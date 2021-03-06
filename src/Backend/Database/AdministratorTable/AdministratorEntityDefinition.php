<?php declare(strict_types = 1);
namespace SpawnBackend\Database\AdministratorTable;


use DateTime;
use SpawnCore\System\Database\Entity\Entity;
use SpawnCore\System\Database\Entity\EntityTraits\EntityCreatedAtTrait;
use SpawnCore\System\Database\Entity\EntityTraits\EntityIDTrait;
use SpawnCore\System\Database\Entity\EntityTraits\EntityUpdatedAtTrait;

class AdministratorEntityDefinition extends Entity
{
    use EntityIDTrait;
    use EntityUpdatedAtTrait;
    use EntityCreatedAtTrait;

    protected string $username;
    protected string $password;
    protected string $email;
    protected bool $active;
    protected ?string $loginHash;
    protected ?DateTime $loginExpiration;

    public function __construct(
        string $username,
        string $password,
        string $email,
        bool $active = true,
        ?string $loginHash = null,
        ?DateTime $loginExpiration = null,
        ?string $id = null,
        ?DateTime $createdAt = null,
        ?DateTime $updatedAt = null
    )
    {
        $this->setUsername($username);
        $this->setPassword($password);
        $this->setEmail($email);
        $this->setActive($active);
        $this->setLoginHash($loginHash);
        $this->setLoginExpiration($loginExpiration);
        $this->setId($id);
        $this->setUpdatedAt($updatedAt);
        $this->setCreatedAt($createdAt);
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
            'createdAt' => $this->getCreatedAt(),
            'loginHash' => $this->getLoginHash(),
            'loginExpiration' => $this->getLoginExpiration(),
        ];
    }

    public static function getEntityFromArray(array $values): Entity
    {
        $values['updatedAt'] = self::getDateTimeFromVariable($values['updatedAt']??null);
        $values['createdAt'] = self::getDateTimeFromVariable($values['createdAt']??null);
        $values['loginExpiration'] = self::getDateTimeFromVariable($values['loginExpiration']??null);

        return new AdministratorEntity(
            $values['username'],
            $values['password'],
            $values['email'],
            (bool)$values['active'],
            $values['loginHash'] ?? null,
            $values['loginExpiration'] ?? null,
            $values['id'] ?? null,
            $values['createdAt'] ?? null,
            $values['updatedAt'] ?? null
        );
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;
        return $this;
    }


    public function getLoginHash(): ?string
    {
        return $this->loginHash;
    }

    public function setLoginHash(?string $loginHash): self
    {
        $this->loginHash = $loginHash;
        return $this;
    }

    public function getLoginExpiration(): ?DateTime
    {
        return $this->loginExpiration;
    }

    public function setLoginExpiration(?DateTime $loginExpiration): self
    {
        $this->loginExpiration = $loginExpiration;
        return $this;
    }

}