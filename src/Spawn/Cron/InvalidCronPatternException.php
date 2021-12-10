<?php

namespace spawnCore\Cron;

use spawn\system\Throwables\AbstractException;
use Throwable;

class InvalidCronPatternException extends AbstractException {

    public function __construct(string $invalidPattern, Throwable $previous = null)
    {
        parent::__construct(['pattern' => $invalidPattern], $previous);
    }

    protected function getMessageTemplate(): string
    {
        return 'Encountered invalid cron pattern: "%pattern%"!';
    }

    protected function getExitCode(): int
    {
        return 813;
    }
}