<?php

namespace SpawnCore\System\Custom\Throwables;


use Exception;
use Throwable;

class FailedConvertToReflectionObjectException extends AbstractException
{

    public function __construct($class, $method, Throwable $previous = null)
    {
        if (!is_scalar($class)) {
            try {
                $class = (string)$class;
            } catch (Exception $e) {
                $class = json_encode($class, JSON_THROW_ON_ERROR);
            }
        }

        if (!is_scalar($method)) {
            try {
                $class = (string)$class;
            } catch (Exception $e) {
                $class = json_encode($class, JSON_THROW_ON_ERROR);
            }
        }

        parent::__construct([
            'class' => $class,
            'method' => $method,
        ], $previous);
    }

    protected function getMessageTemplate(): string
    {
        return 'Error when converting data to ReflectionObject with class "%class%" and method "%method%"!';
    }

    protected function getExitCode(): int
    {
        return 37;
    }
}