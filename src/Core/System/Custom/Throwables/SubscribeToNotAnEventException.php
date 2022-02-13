<?php

namespace SpawnCore\System\Custom\Throwables;

use Throwable;

class SubscribeToNotAnEventException extends AbstractException
{

    public function __construct(string $classThatIsNotAnEvent, Throwable $previous = null)
    {
        parent::__construct([
            'class' => $classThatIsNotAnEvent
        ], $previous);
    }

    protected function getMessageTemplate(): string
    {
        return 'The class %class% could not be subscribed! Please subscribe only to subclasses of the Event class!';
    }

    protected function getExitCode(): int
    {
        return 40;
    }
}