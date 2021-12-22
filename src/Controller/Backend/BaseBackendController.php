<?php

namespace spawnApp\Controller\Backend;


use spawnApp\Database\MigrationTable\MigrationRepository;
use spawnCore\Custom\FoundationStorage\AbstractBackendController;
use spawnCore\Custom\Response\AbstractResponse;
use spawnCore\Custom\Response\TwigResponse;
use spawnCore\Custom\Throwables\DatabaseConnectionException;
use spawnCore\Database\Criteria\Criteria;
use spawnCore\Database\Entity\RepositoryException;

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
     * @throws DatabaseConnectionException
     * @throws RepositoryException
     */
    public function homeAction(): AbstractResponse {
       /** @var MigrationRepository $migrationRepository */
        $migrationRepository = $this->container->getServiceInstance('system.repository.migrations');
        $migrationCollection = $migrationRepository->search(new Criteria());


        $this->twig->assign('content_file', 'backend/contents/home/content.html.twig');
        return new TwigResponse('backend/index.html.twig');
    }
}