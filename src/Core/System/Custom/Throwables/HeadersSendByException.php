<?php declare(strict_types=1);

namespace SpawnCore\System\Custom\Throwables;

class HeadersSendByException extends AbstractException
{

    protected function getMessageTemplate(): string
    {
        return 'The headers were already send by!';
    }

    protected function getExitCode(): int
    {
        return 51;
    }
}