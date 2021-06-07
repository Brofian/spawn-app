<?php

namespace webuApp\Controller;

use webu\system\Core\Base\Controller\ControllerInterface;
use webuApp\Services\TestService;

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