<?php

namespace spawnApp\Controller;


use spawn\system\Core\Base\Controller\AbstractController;

class Fallback404Controller extends AbstractController  {

    public function error404Action() {

        dd("404 default");
    }

}