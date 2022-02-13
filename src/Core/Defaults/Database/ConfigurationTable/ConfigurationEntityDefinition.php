<?php

namespace SpawnCore\Defaults\Database\ConfigurationTable;


use DateTime;
use Exception;
use SpawnCore\System\Database\Entity\Entity;
use SpawnCore\System\Database\Entity\EntityTraits\EntityCreatedAtTrait;
use SpawnCore\System\Database\Entity\EntityTraits\EntityIDTrait;
use SpawnCore\System\Database\Entity\EntityTraits\EntityUpdatedAtTrait;

class ConfigurationEntityDefinition extends Entity
{
    use EntityIDTrait;
    use EntityUpdatedAtTrait;
    use EntityCreatedAtTrait;

    protected string $internalName;
    protected string $type;
    protected ?string $value;
    protected array $definition = [];
    protected string $folder;

    /**
     * @param string|array $definition
     */
    public function __construct(
        string $internalName,
        string $type,
        ?string $value,
        $definition,
        string $folder,
        ?string $id = null,
        ?DateTime $createdAt = null,
        ?DateTime $updatedAt = null
    )
    {
        $this->setInternalName($internalName);
        $this->setType($type);
        $this->setValue($value);
        $this->setDefinition($definition);
        $this->setFolder($folder);
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
            'internalName' => $this->getInternalName(),
            'type' => $this->getType(),
            'value' => $this->getValue(),
            'definition' => $this->getDefinition(),
            'folder' => $this->getFolder(),
            'updatedAt' => $this->getUpdatedAt(),
            'createdAt' => $this->getCreatedAt(),
        ];
    }

    public static function getEntityFromArray(array $values): Entity
    {
        $values['updatedAt'] = self::getDateTimeFromVariable($values['updatedAt']??null);
        $values['createdAt'] = self::getDateTimeFromVariable($values['createdAt']??null);

        return new ConfigurationEntity(
            $values['internalName'],
            $values['type'],
            $values['value'],
            $values['definition'],
            $values['folder'],
            $values['id'] ?? null,
            $values['createdAt'] ?? null,
            $values['updatedAt'] ?? null
        );
    }

    public function getInternalName(): string
    {
        return $this->internalName;
    }

    public function setInternalName(string $internalName): self
    {
        $this->internalName = $internalName;
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string|array
     */
    public function getDefinition(bool $asArray = false)
    {
        if($asArray) {
            return $this->definition;
        }
        return json_encode($this->definition, JSON_THROW_ON_ERROR);
    }

    /**
     * @param string|array $definition
     */
    public function setDefinition($definition): self
    {
        if(is_array($definition)) {
            $this->definition = $definition;
        }
        elseif(is_string($definition)) {
            try {
                $this->definition = json_decode($definition, true, 999, JSON_THROW_ON_ERROR);
            }
            catch (Exception $e) {}
        }

        return $this;
    }

    public function getFolder(): string
    {
        return $this->folder;
    }

    public function setFolder(string $folder): self
    {
        $this->folder = $folder;
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