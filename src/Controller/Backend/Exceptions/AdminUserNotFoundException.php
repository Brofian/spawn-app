<?php

namespace spawnApp\Controller\Backend\Exceptions;


use spawnCore\Custom\Throwables\AbstractException;
use Throwable;

class AdminUserNotFoundException extends AbstractException {

    public function __construct(Throwable $previous = null)
    {
        parent::__construct([], $previous);
    }

    protected function getMessageTemplate(): string
    {
        return 'No user with this username and password found!';
    }

    protected function getExitCode(): int
    {
        return 71;
    }
}