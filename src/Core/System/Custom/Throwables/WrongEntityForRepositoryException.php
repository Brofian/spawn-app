<?php declare(strict_types=1);

namespace SpawnCore\System\Custom\Throwables;

use Throwable;

class WrongEntityForRepositoryException extends AbstractException
{
    public function __construct(string $entityClass, string $desiredClass, $repositoryClass, Throwable $previous = null)
    {
        parent::__construct(
            [
                'entityClass' => $entityClass,
                'desiredClass' => $desiredClass,
                'repositoryClass' => $repositoryClass,
            ],
            $previous
        );
    }

    protected function getMessageTemplate(): string
    {
        return 'The Repository "%repositoryClass%" expects an entity of type "%desiredClass%". Entity of class "%entityClass%" given!';
    }

    protected function getExitCode(): int
    {
        return 51;
    }
}