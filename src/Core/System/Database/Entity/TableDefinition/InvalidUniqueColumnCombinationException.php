<?php declare(strict_types = 1);
namespace SpawnCore\System\Database\Entity\TableDefinition;

use SpawnCore\System\Custom\Throwables\AbstractException;
use Throwable;

class InvalidUniqueColumnCombinationException extends AbstractException {

    public function __construct(string $error, Throwable $previous = null)
    {
        parent::__construct([
            'error' => $error
        ], $previous);
    }

    protected function getMessageTemplate(): string
    {
        return '%error%';
    }

    protected function getExitCode(): int
    {
        return 160;
    }
}