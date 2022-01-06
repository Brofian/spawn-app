<?php

namespace spawnCore\Database\Entity\TableDefinition;

use spawnCore\Custom\Throwables\AbstractException;
use Throwable;

class InvalidForeignKeyConstraintException extends AbstractException {

    public function __construct(string $error, Throwable $previous = null)
    {
        parent::__construct([
            'error' => $error
        ], $previous);
    }

    protected function getMessageTemplate(): string
    {
        return '%error%';
    }

    protected function getExitCode(): int
    {
        return 160;
    }
}