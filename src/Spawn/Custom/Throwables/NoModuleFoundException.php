<?php

namespace spawnCore\Custom\Throwables;

class NoModuleFoundException extends AbstractException
{
    protected function getMessageTemplate(): string
    {
        return 'No available Module was found! Please try running <span style=\'background:#21c2ff; padding: 0 5px\'>bin/console modules:refresh</span>';
    }

    protected function getExitCode(): int
    {
        return 53;
    }
}