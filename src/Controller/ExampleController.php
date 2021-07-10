<?php

namespace spawnApp\Controller;

use spawn\system\Core\Base\Controller\ControllerInterface;
use spawnApp\Services\TestService;

class ExampleController implements ControllerInterface  {


    public static function defaultActionPaths(): array
    {
        return [
            'index' => '/'
        ];
    }


    public function indexAction() {
        dd("index example");
    }

}