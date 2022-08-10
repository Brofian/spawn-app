<?php declare(strict_types=1);

namespace SpawnCore\System\Database\Entity;


use DateTime;
use Exception;
use SpawnCore\System\Custom\FoundationStorage\Mutable;
use SpawnCore\System\Database\Entity\EntityTraits\EntityPayloadTrait;

abstract class Entity extends Mutable
{
    use EntityPayloadTrait;

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

        if($array === null) {
            return [];
        }

        try {
            return json_decode($array, true, 512, JSON_THROW_ON_ERROR);
        } catch (Exception $e) {
            return [$array];
        }
    }

    public function applyValues(array $values): void {
        foreach($values as $name => $value) {
            $type = get_debug_type($this->$name);

            switch ($type) {
                case "int":
                    $value = (int)$value;
                    break;
                case "bool":
                    $value = (bool)$value;
                    break;
                case "float":
                    $value = (float)$value;
                    break;
                case DateTime::class:
                    $value = new DateTime($value);
                    break;
            }

            $methodName = 'set'.ucfirst($name);
            if(method_exists($this, $methodName)) {
                $this->{$methodName}($value);
            }
        }
    }

}