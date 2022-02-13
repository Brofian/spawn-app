<?php

namespace SpawnCore\System\Custom\Response\Exceptions;

use SpawnCore\System\Custom\Throwables\AbstractException;
use Throwable;

abstract class AbstractResponseException extends AbstractException
{

    public function __construct(string $responseType, $data, Throwable $previous = null)
    {
        parent::__construct(
            [
                'responseType' => $responseType,
                'data' => $data
            ]
            , $previous);
    }

    protected function getMessageTemplate(): string
    {
        return 'Cannot create %responseType% Response from data %data%';
    }

    protected function getExitCode(): int
    {
        return 70;
    }
}