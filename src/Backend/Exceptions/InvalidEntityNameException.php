<?php

namespace SpawnBackend\Exceptions;


use SpawnCore\System\Custom\Throwables\AbstractException;
use Throwable;

class InvalidEntityNameException extends AbstractException {

    public function __construct(string $entityName, Throwable $previous = null)
    {
        parent::__construct([
            'entityName' => $entityName
        ], $previous);
    }

    protected function getMessageTemplate(): string
    {
        return 'Invalid entity name "%entityName%"!';
    }

    protected function getExitCode(): int
    {
        return 913;
    }
}