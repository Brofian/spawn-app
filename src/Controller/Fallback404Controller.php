<?php

namespace spawnApp\Controller;


use spawnCore\Custom\FoundationStorage\AbstractController;
use spawnCore\Custom\Response\AbstractResponse;
use spawnCore\Custom\Response\SimpleResponse;

class Fallback404Controller extends AbstractController  {




    /**
     * @route /404
     * @return AbstractResponse
     */
    public function error404Action(): AbstractResponse {
        return new SimpleResponse('<h1>404 default</h1>');
    }

}