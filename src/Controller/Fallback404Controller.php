<?php

namespace spawnApp\Controller;


use spawn\system\Core\Base\Controller\AbstractController;
use spawn\system\Core\Contents\Response\AbstractResponse;
use spawn\system\Core\Contents\Response\SimpleResponse;

class Fallback404Controller extends AbstractController  {

    /**
     * @route /404
     * @return AbstractResponse
     */
    public function error404Action(): AbstractResponse {
        return new SimpleResponse('<h1>404 default</h1>');
    }

}