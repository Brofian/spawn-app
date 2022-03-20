<?php

namespace SpawnBackend\Controller;


use SpawnCore\System\Custom\FoundationStorage\AbstractController;
use SpawnCore\System\Custom\Response\AbstractResponse;
use SpawnCore\System\Custom\Response\SimpleResponse;

class Fallback404Controller extends AbstractController  {




    /**
     * @route /404
     * @return AbstractResponse
     */
    public function error404Action(): AbstractResponse {
        return new SimpleResponse('<h1>404 default</h1>');
    }

}