<?php

namespace spawnCore\Custom\Response;

abstract class AbstractResponse
{

    protected string $responseText = '';

    public function __construct(string $responseText)
    {
        $this->responseText = $responseText;
    }

    public function getResponse(): string
    {
        return $this->responseText;
    }

}