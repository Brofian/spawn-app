<?php declare(strict_types = 1);
namespace SpawnCore\System\Custom\Response;

class SimpleResponse extends AbstractResponse
{

    public function __construct(string $responseText, ?CacheControlState $cache = null)
    {
        parent::__construct($responseText, $cache);
    }

}