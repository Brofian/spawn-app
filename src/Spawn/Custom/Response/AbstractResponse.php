<?php

namespace spawnCore\Custom\Response;

abstract class AbstractResponse
{
    protected string $responseText = '';
    protected CacheControlState $cacheControlState;

    public function __construct(string $responseText, ?CacheControlState $cache = null)
    {
        $this->responseText = $responseText;
        $this->cacheControlState = $cache ?? new CacheControlState();
    }

    public function getCacheStatus(): CacheControlState {
        return $this->cacheControlState;
    }

    public function getResponse(): string
    {
        return $this->responseText;
    }

}