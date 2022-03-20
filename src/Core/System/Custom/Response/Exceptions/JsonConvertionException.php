<?php declare(strict_types = 1);
namespace SpawnCore\System\Custom\Response\Exceptions;

use Throwable;

class JsonConvertionException extends AbstractResponseException
{

    public function __construct($data, Throwable $previous = null)
    {
        parent::__construct('JSON', $data, $previous);
    }


}