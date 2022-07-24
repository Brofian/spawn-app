<?php declare(strict_types = 1);
namespace SpawnBackend\Controller\Backend;


use SpawnCore\Defaults\Database\MigrationTable\MigrationRepository;
use SpawnCore\System\Custom\FoundationStorage\AbstractBackendController;
use SpawnCore\System\Custom\Response\AbstractResponse;
use SpawnCore\System\Custom\Response\TwigResponse;
use SpawnCore\System\Custom\Throwables\DatabaseConnectionException;
use SpawnCore\System\Database\Criteria\Criteria;
use SpawnCore\System\Database\Entity\RepositoryException;

class BaseBackendController extends AbstractBackendController {

    public static function getSidebarMethods(): array
    {
        return [
            //Category
            'home' => [
                'title' => "home", //Category title
                'color' => "#ff0000", //Category color
                'actions' => [ //Category actions
                    [
                        'route' => 'app.backend.home',
                        'parameters' => [],
                        'title' => 'testaction' //action title
                    ]
                ]
            ]
        ];
    }

    /**
     * @route /backend
     * @name "app.backend.home"
     * @requires admin
     * @locked
     * @return AbstractResponse
     * @throws DatabaseConnectionException
     * @throws RepositoryException
     */
    public function homeAction(): AbstractResponse {
       /** @var MigrationRepository $migrationRepository */
        $migrationRepository = $this->container->getServiceInstance('migrations.repository');
        $migrationCollection = $migrationRepository->search(new Criteria());


        $this->twig->assign('content_file', 'backend/contents/home/content.html.twig');
        return new TwigResponse('backend/index.html.twig');
    }
}