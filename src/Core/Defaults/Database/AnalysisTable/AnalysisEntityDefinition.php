<?php declare(strict_types = 1);

namespace SpawnCore\Defaults\Database\AnalysisTable;

use DateTime;
use SpawnCore\System\Database\Entity\Entity;
use SpawnCore\System\Database\Entity\EntityTraits\EntityCreatedAtTrait;
use SpawnCore\System\Database\Entity\EntityTraits\EntityIDTrait;


class AnalysisEntityDefinition extends Entity
{
    use EntityIDTrait;
    use EntityCreatedAtTrait;

    protected ?string $url_id;
    protected ?string $dataJson;
    protected bool $bot;


    public function __construct(
        ?string $url_id,
        ?string $dataJson,
        bool $bot,
        ?string $id = null,
        ?DateTime $createdAt = null
    )
    {
        $this->setBot($bot);
        $this->setUrlId($url_id);
        $this->setDataJson($dataJson);
        $this->setId($id);
        $this->setCreatedAt($createdAt);
    }


    public function getRepositoryClass(): string
    {
        return AnalysisRepository::class;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'urlId' => $this->getUrlId(),
            'data' => $this->getDataJson(),
            'bot' => $this->isBot(),
            'createdAt' => $this->getCreatedAt(),
        ];
    }

    public static function getEntityFromArray(array $values): self
    {
        $values['createdAt'] = self::getDateTimeFromVariable($values['createdAt']??null);

        return new AnalysisEntity(
            $values['urlId'],
            $values['data'],
            (bool)$values['bot'],
            $values['id'] ?? null,
            $values['createdAt'] ?? null,
        );
    }

    public function getUrlId(): ?string
    {
        return $this->url_id;
    }

    public function setUrlId(?string $url_id): self
    {
        $this->url_id = $url_id;
        return $this;
    }

    public function getDataJson(): ?string
    {
        return $this->dataJson;
    }

    public function setDataJson(?string $dataJson): self
    {
        $this->dataJson = $dataJson;
        return $this;
    }

    public function isBot(): bool
    {
        return $this->bot;
    }

    public function setBot(bool $bot): self
    {
        $this->bot = $bot;
        return $this;
    }





}