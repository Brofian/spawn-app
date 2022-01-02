<?php

namespace spawnApp\Services\Exceptions;

use spawnCore\Custom\Throwables\AbstractException;
use Throwable;

class MissingRequiredConfigurationFieldException extends AbstractException {

    public function __construct(string $missingElement, string $fieldType, Throwable $previous = null)
    {
        parent::__construct([
            'element' => $missingElement,
            'type' => $fieldType
        ], $previous);
    }

    protected function getMessageTemplate(): string
    {
        return 'Missing required element "%element%" for config field type "%type%"!';
    }

    protected function getExitCode(): int
    {
        return 142;
    }
}