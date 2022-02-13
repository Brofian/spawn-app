<?php

namespace SpawnCore\System\Custom\Response;

use Exception;
use SpawnCore\System\Custom\Response\Exceptions\JsonConvertionException;

class JsonResponse extends AbstractResponse
{

    public function __construct(array $responseArray, ?CacheControlState $cache = null)
    {
        try {
            $jsonResponse = json_encode($responseArray);
        } catch (Exception $e) {
            $jsonResponse = (string)(new JsonConvertionException($responseArray));
        }

        parent::__construct($jsonResponse, $cache);
    }

}