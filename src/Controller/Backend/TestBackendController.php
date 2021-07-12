<?php

namespace spawnApp\Controller\Backend;

use spawn\system\Core\Base\Controller\AbstractBackendController;

class TestBackendController extends AbstractBackendController {

    public static function getSidebarMethods(): array
    {
        return [
            'Home' => [
                'actions' => [
                    [
                        'controller' => '%self.key%',
                        'action' => 'testAction',
                        'title' => 'Test'
                    ]
                ]
            ]
        ];
    }


    public function testAction() {
        $this->twig->assign('content_file', 'backend/test/content.html.twig');
    }
}