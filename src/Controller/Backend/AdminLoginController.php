<?php

namespace spawnApp\Controller\Backend;

use http\Exception\InvalidArgumentException;
use spawn\system\Core\Base\Controller\AbstractBackendController;
use spawn\system\Core\Base\Database\Definition\TableDefinition\ColumnDefinition;
use spawn\system\Core\Contents\Response\AbstractResponse;
use spawn\system\Core\Contents\Response\JsonResponse;
use spawn\system\Core\Contents\Response\TwigResponse;
use spawn\system\Core\Custom\CSRFTokenAssistant;
use spawn\system\Core\Request;
use spawn\system\Core\Services\Service;
use spawn\system\Core\Services\ServiceContainerProvider;
use spawnApp\Extensions\Exceptions\HoneypotException;
use spawnApp\Services\AdminLoginManager;

class AdminLoginController extends AbstractBackendController {

    protected AdminLoginManager $adminLoginManager;
    protected CSRFTokenAssistant $csrfTokenAssistant;

    public function __construct(
        AdminLoginManager $adminLoginManager,
        CSRFTokenAssistant $csrfTokenAssistant
    )
    {
        parent::__construct();
        $this->adminLoginManager = $adminLoginManager;
        $this->csrfTokenAssistant = $csrfTokenAssistant;
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
            $serviceContainer = ServiceContainerProvider::getServiceContainer();

            /** @var Request $request */
            $request = $serviceContainer->getServiceInstance('system.kernel.request');
            $post = $request->getPost();

            //honeypot
            if($post->get('age')) {
                throw new HoneypotException();
            }

            //csrf validation
            $this->csrfTokenAssistant->validateToken($post->get('csrf'), 'admin.login.token');

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


    /**
     * @route /backend/logout
     * @locked
     * @return AbstractResponse
     */
    public function logoutAction(): AbstractResponse {
        $errors = [];

        if($this->adminLoginManager->isAdminLoggedIn()) {
            try {
                $this->adminLoginManager->logoutAdmin();
            }
            catch (\Exception $e) {
                $errors[] = $e->getMessage();
            }
        }

        $wasSuccess = empty($errors);
        return new JsonResponse([
            'success' => $wasSuccess,
            'reload' => $wasSuccess,
            'errors' => $errors
        ]);
    }

}