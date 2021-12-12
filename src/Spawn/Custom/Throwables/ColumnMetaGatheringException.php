<?php

namespace spawnCore\Custom\Throwables;

use Throwable;

class ColumnMetaGatheringException extends AbstractException
{
    public function __construct(string $table, Throwable $previous = null)
    {
        parent::__construct(
            [
                'table' => $table
            ],
            $previous
        );
    }

    protected function getMessageTemplate(): string
    {
        return 'Could not gather column meta for table "%table%"!';
    }

    protected function getExitCode(): int
    {
        return 53;
    }
}