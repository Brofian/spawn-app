<?php

namespace spawnApp\Controller\Backend;


use spawnApp\Database\MigrationTable\MigrationRepository;
use spawnCore\Custom\FoundationStorage\AbstractBackendController;
use spawnCore\Custom\Response\AbstractResponse;
use spawnCore\Custom\Response\TwigResponse;

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