<?php declare(strict_types = 1);
namespace SpawnCore\System\Custom\Response;

use Exception;
use SpawnCore\System\Custom\Response\Exceptions\JsonConvertionException;

class JsonResponse extends AbstractResponse
{

    public function __construct(array $responseArray, ?CacheControlState $cache = null)
    {
        if(MODE !== 'dev' && isset($responseArray['errors'])) {
            unset($responseArray['errors']);
        }

        try {
            $jsonResponse = json_encode($responseArray, JSON_THROW_ON_ERROR);
        } catch (Exception $e) {
            $jsonResponse = (string)(new JsonConvertionException($responseArray));
        }

        parent::__construct($jsonResponse, $cache);
    }

}