<?php

namespace spawnApp\Controller\Backend;

use spawn\system\Core\Base\Controller\AbstractBackendController;
use spawn\system\Core\Base\Database\Definition\TableDefinition\ColumnDefinition;
use spawn\system\Core\Contents\Response\AbstractResponse;
use spawn\system\Core\Contents\Response\TwigResponse;
use spawn\system\Core\Response;
use spawnApp\Database\MigrationTable\MigrationRepository;

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

    /**
     * @route /backend
     * @locked
     * @return AbstractResponse
     */
    public function homeAction(): AbstractResponse {

        /** @var MigrationRepository $migrationRepository */
        $migrationRepository = $this->container->getServiceInstance('system.repository.migrations');
        $migrationCollection = $migrationRepository->search();


        $this->twig->assign('content_file', 'backend/contents/home/content.html.twig');
        return new TwigResponse('backend/index.html.twig');
    }
}