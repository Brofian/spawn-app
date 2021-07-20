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
            ]
        ];
    }


    public function homeAction() {

        $this->twig->assign('content_file', 'backend/contents/home/content.html.twig');
    }
}