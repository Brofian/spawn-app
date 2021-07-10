<?php

namespace spawnApp\Controller;

use spawn\system\Core\Base\Controller\ControllerInterface;

class Fallback404Controller implements ControllerInterface  {

    public static function defaultActionPaths(): array
    {
        return [
            'index' => '/error/404'
        ];
    }


    public function error404Action() {
        dd("404 default");
    }

}