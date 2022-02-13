<?php

namespace SpawnCore\System\Custom\FoundationStorage;

abstract class AbstractCommand
{

    abstract public static function getCommand(): string;

    abstract public static function getShortDescription(): string;

    final public static function createParameterArray(array $arguments): array
    {
        $parameters = static::getParameters();
        $values = [];

        foreach ($parameters as $parameter => $keys) {
            $values[$parameter] = null;

            if (is_array($keys)) {
                foreach ($keys as $key) {
                    if (isset($arguments[$key])) {
                        $values[$parameter] = $arguments[$key];
                        break;
                    }
                }
            } elseif (is_string($keys) && isset($arguments[$keys])) {
                $values[$parameter] = $arguments[$keys];
            }
        }

        return $values;
    }

    abstract public static function getParameters(): array;

    /**
     * The executed content of the command. Should return 0 when successful.
     * @param array $parameters
     * @return int
     */
    abstract public function execute(array $parameters): int;

}