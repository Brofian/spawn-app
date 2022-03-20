<?php

namespace SpawnBackend\Exceptions;


use SpawnCore\System\Custom\Throwables\AbstractException;

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