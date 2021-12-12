<?php

namespace spawnCore\Custom\Response;

use Exception;
use spawnCore\Custom\Response\Exceptions\JsonConvertionException;

class JsonResponse extends AbstractResponse
{

    public function __construct(array $responseArray)
    {
        try {
            $jsonResponse = json_encode($responseArray);
        } catch (Exception $e) {
            $jsonResponse = (string)(new JsonConvertionException($responseArray));
        }

        parent::__construct($jsonResponse);
    }

}