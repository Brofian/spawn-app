<?php declare(strict_types=1);

namespace SpawnCore\System\Custom\Throwables;

use Throwable;

class DatabaseConnectionException extends AbstractException
{
    public function __construct(
        string $host,
        string $name,
        string $port,
        string $driver,
        string $username,
        string $password,
        Throwable $previous = null
    )
    {
        parent::__construct(
            [
                'host' => $host,
                'name' => $name,
                'port' => $port,
                'driver' => $driver,
                'username' => $username,
                'password' => $password,
            ],
            $previous
        );
    }

    protected function getMessageTemplate(): string
    {
        return 'The Connection to the Database cant be established with parameters: ' .
            'host=%host% ' .
            'name=%name% ' .
            'port=%port% ' .
            'driver=%driver% ' .
            'username=%username% ' .
            'password=%password%!';
    }

    protected function getExitCode(): int
    {
        return 50;
    }
}