<?php declare(strict_types=1);

namespace SpawnBackend\Controller\Backend;

use SpawnCore\Defaults\Services\UserManager;
use SpawnCore\System\CardinalSystem\Request;
use SpawnCore\System\CardinalSystem\Response;
use SpawnCore\System\Custom\FoundationStorage\AbstractBackendController;
use SpawnCore\System\Custom\Response\AbstractResponse;
use SpawnCore\System\Custom\Response\TwigResponse;

class UserController extends AbstractBackendController {

    protected UserManager $userManager;
    protected Request $request;

    public function __construct(
        UserManager $userManager,
        Request $request
    )   {
        parent::__construct();
        $this->userManager = $userManager;
        $this->request = $request;
    }


    public static function getSidebarMethods(): array
    {
        return [
            'configuration' => [
                'title' => "config",
                'color' => "#00ff00",
                'actions' => [
                    [
                        'route' => 'app.backend.user.overview',
                        'parameters' => [],
                        'title' => 'users'
                    ]
                ]
            ]
        ];
    }

    /**
     * @route /backend/user/overview
     * @name "app.backend.user.overview"
     * @locked
     * @return AbstractResponse
     */
    public function userOverviewAction(): AbstractResponse {
        $get = $this->request->getGet();

        $numberOfEntriesPerPage = (int)($get->get('num', 20) ?? 1);
        $page = max((int)($get->get('page', 1) ?? 1), 1);
        $totalNumberOfEntries = ($this->userManager->getTotalNumberOfUsers() ?? 1);
        $availablePages = (int)ceil($totalNumberOfEntries / $numberOfEntriesPerPage);
        $users = $this->userManager->getUsers($numberOfEntriesPerPage, ($page-1)*$numberOfEntriesPerPage);

        $this->twig->assignBulk([
            'table_info' => [
                'page' => $page,
                'entriesPerPage' => $numberOfEntriesPerPage,
                'availablePages' => $availablePages,
            ],
            'users' => $users,
            'content_file' => 'backend/contents/users/overview/content.html.twig'
        ]);

        return new TwigResponse('backend/index.html.twig');
    }


}


