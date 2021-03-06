<?php declare(strict_types=1);

namespace SpawnCore\System\Database\Entity;


use DateTime;
use Exception;
use SpawnCore\System\Custom\FoundationStorage\Mutable;
use SpawnCore\System\Database\Entity\EntityTraits\EntityIDTrait;
use SpawnCore\System\Database\Entity\EntityTraits\EntityPayloadTrait;

abstract class Entity extends Mutable
{
    use EntityPayloadTrait;

    abstract public function getRepositoryClass(): string;

    abstract public function toArray(): array;

    abstract public static function getEntityFromArray(array $values): Entity;

    protected static function getDateTimeFromVariable($dateTime): DateTime
    {

        if ($dateTime instanceof DateTime) {
            return $dateTime;
        }

        try {
            if(is_string($dateTime)) {
                return new DateTime($dateTime);
            }
            return new DateTime();

        } catch (Exception $e) {
            return new DateTime();
        }
    }

    protected static function getArrayFromVariable($array): array
    {

        if (is_array($array)) {
            return $array;
        }

        try {
            return json_decode($array, true, 512, JSON_THROW_ON_ERROR);
        } catch (Exception $e) {
            return [$array];
        }
    }


}