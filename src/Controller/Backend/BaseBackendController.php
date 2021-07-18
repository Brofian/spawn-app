<?php

namespace spawnApp\Controller\Backend;

use spawn\system\Core\Base\Controller\AbstractBackendController;

class BaseBackendController extends AbstractBackendController {

    public static function getSidebarMethods(): array
    {
        return [
            'Home' => [ //Kategory
                'title' => "Home", //Kategory title
                'color' => "#ff0000", //Kategory color
                'actions' => [ //Kategory actions
                    [
                        'controller' => '%self.key%',
                        'action' => 'homeAction', //action
                        'title' => 'Home Link' //action title
                    ]
                ]
            ],
            'Test' => [
                'title' => 'Test',
                'color' => '#00ff00',
                'actions' => [
                    [
                        'controller' => '%self.key%',
                        'action' => 'homeAction',
                        'title' => 'testLink'
                    ]
                ]
            ]
        ];
    }


    public function homeAction() {

        $this->twig->assign('content_file', 'backend/home/content.html.twig');
    }
}