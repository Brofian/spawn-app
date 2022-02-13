<?php

namespace SpawnCore\System\Database\Entity;

use SpawnCore\System\Custom\Throwables\AbstractException;
use Throwable;

class RepositoryException extends AbstractException {

    public function __construct(string $exceptionMessage, Throwable $previous = null)
    {
        parent::__construct([
            'message' => $exceptionMessage
        ], $previous);
    }


    protected function getMessageTemplate(): string
    {
        return '%message%';
    }

    protected function getExitCode(): int
    {
        return 189;
    }
}