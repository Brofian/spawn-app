<?php

namespace spawnApp\Controller\Backend;

use spawn\system\Core\Base\Controller\AbstractBackendController;
use spawn\system\Core\Base\Database\Definition\TableDefinition\ColumnDefinition;
use spawn\system\Core\Contents\Response\AbstractResponse;
use spawn\system\Core\Contents\Response\JsonResponse;
use spawn\system\Core\Contents\Response\SimpleResponse;
use spawn\system\Core\Contents\Response\TwigResponse;
use spawn\system\Core\Request;
use spawn\system\Core\Response;
use spawn\system\Core\Services\ServiceContainerProvider;
use spawnApp\Database\MigrationTable\MigrationRepository;
use spawnApp\Services\AdminLoginManager;

class AdminLoginController extends AbstractBackendController {

    protected AdminLoginManager $adminLoginManager;

    public function __construct(
        AdminLoginManager $adminLoginManager
    )
    {
        parent::__construct();
        $this->adminLoginManager = $adminLoginManager;
    }


    public static function getSidebarMethods(): array
    {
        return [];
    }

    /**
     * @route /backend/login
     * @locked
     * @return AbstractResponse
     */
    public function loginAction(): AbstractResponse {

        return new TwigResponse('backend/login/page.html.twig');
    }


    /**
     * @route /backend/login/submit
     * @locked
     * @return AbstractResponse
     */
    public function loginSubmitAction(): AbstractResponse {
        $errors = [];

        try {
            /** @var Request $request */
            $request = ServiceContainerProvider::getServiceContainer()->getServiceInstance('system.kernel.request');

            $post = $request->getPost();
            $this->adminLoginManager->tryAdminLogin(
                $post->get('username'),
                $post->get('password')
            );
        }
        catch (\Exception $e) {
            $errors[] = $e->getMessage();
        }


        $wasSuccess = empty($errors);
        return new JsonResponse([
            'success' => $wasSuccess,
            'reload' => $wasSuccess,
            'errors' => $errors
        ]);
    }


}