<?php declare(strict_types=1);

namespace spawnCore\Database\Entity;


use Cassandra\Date;
use DateTime;
use Exception;
use spawnCore\Custom\FoundationStorage\Mutable;
use spawnCore\Database\Entity\EntityTraits\EntityPayloadTrait;

abstract class Entity extends Mutable
{
    use EntityPayloadTrait;

    public abstract function getRepositoryClass(): string;

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
            return json_decode($array, true);
        } catch (Exception $e) {
            return [$array];
        }
    }


}