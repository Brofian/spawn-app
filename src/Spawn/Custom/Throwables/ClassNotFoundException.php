<?php declare(strict_types=1);

namespace spawnCore\Custom\Throwables;

use Throwable;

class ClassNotFoundException extends AbstractException
{
    public function __construct(string $class, Throwable $previous = null)
    {
        parent::__construct(
            [
                'class' => $class
            ],
            $previous
        );
    }

    protected function getMessageTemplate(): string
    {
        return 'The class "%class%" could\'nt be loaded! Check if it exists!';
    }

    protected function getExitCode(): int
    {
        return 50;
    }
}