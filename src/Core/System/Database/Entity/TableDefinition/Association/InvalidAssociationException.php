<?php

namespace SpawnCore\System\Database\Entity\TableDefinition\Association;

use SpawnCore\System\Custom\Throwables\AbstractException;
use Throwable;

class InvalidAssociationException extends AbstractException {

    public function __construct(string $invalidEntityName, Throwable $previous = null)
    {
        parent::__construct([
            'invalidEntity' => $invalidEntityName
        ], $previous);
    }

    protected function getMessageTemplate(): string
    {
        return 'Encountered invalid association %invalidEntity%';
    }

    protected function getExitCode(): int
    {
        return 8123;
    }
}