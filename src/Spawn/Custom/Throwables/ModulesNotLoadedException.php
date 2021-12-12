<?php

namespace spawnCore\Custom\Throwables;

class ModulesNotLoadedException extends AbstractException
{

    protected function getMessageTemplate(): string
    {
        return 'There are currently no Modules loaded! Please run <span style=\'background:#21c2ff; padding: 0 5px\'\'>bin/console modules:refresh</span>';
    }

    protected function getExitCode(): int
    {
        return 52;
    }
}