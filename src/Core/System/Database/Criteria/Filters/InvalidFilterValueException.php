<?php

namespace SpawnCore\System\Database\Criteria\Filters;

use SpawnCore\System\Custom\Throwables\AbstractException;
use Throwable;

class InvalidFilterValueException extends AbstractException {

    public function __construct(string $type, string $filter, Throwable $previous = null)
    {
        parent::__construct([
            'type' => $type,
            'filter' => $filter,
        ], $previous);
    }

    protected function getMessageTemplate(): string
    {
        return 'The type %type% is invalid for the value of an %filter% filter!';
    }

    protected function getExitCode(): int
    {
        return 741;
    }
}