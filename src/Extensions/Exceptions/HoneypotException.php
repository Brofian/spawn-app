<?php

namespace spawnApp\Extensions\Exceptions;

use spawn\system\Throwables\AbstractException;

class HoneypotException extends AbstractException {

    protected function getMessageTemplate(): string
    {
        return 'Missing correct access rights';
    }

    protected function getExitCode(): int
    {
        return 91;
    }
}