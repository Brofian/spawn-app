<?php

namespace spawnCore\Custom\Response\Exceptions;

use Throwable;

class TwigConvertionException extends AbstractResponseException
{

    public function __construct($data, Throwable $previous = null)
    {
        parent::__construct('Twig', $data, $previous);
    }


}