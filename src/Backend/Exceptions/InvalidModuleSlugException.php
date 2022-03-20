<?php

namespace SpawnBackend\Exceptions;

use SpawnCore\System\Custom\Throwables\AbstractException;
use Throwable;

class InvalidModuleSlugException extends AbstractException {

    public function __construct(string $invalidModule, array $availableModules, Throwable $previous = null)
    {
        parent::__construct([
            'invalid' => $invalidModule,
            'available' => PHP_EOL . '- ' . implode(PHP_EOL.'- ', $availableModules)
        ], $previous);
    }

    protected function getMessageTemplate(): string
    {
        return 'The requested module "%invalid%" does not exist. You can use these instead: %available%';
    }

    protected function getExitCode(): int
    {
        return 239;
    }
}