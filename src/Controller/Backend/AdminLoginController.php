<?php

namespace spawnApp\Controller\Backend;


use Exception;
use spawnApp\Extensions\Exceptions\HoneypotException;
use spawnApp\Services\AdminLoginManager;
use spawnCore\CardinalSystem\Request;
use spawnCore\Custom\FoundationStorage\AbstractBackendController;
use spawnCore\Custom\Gadgets\CSRFTokenAssistant;
use spawnCore\Custom\Response\AbstractResponse;
use spawnCore\Custom\Response\JsonResponse;
use spawnCore\Custom\Response\TwigResponse;
use spawnCore\ServiceSystem\ServiceContainerProvider;

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
        catch (Exception $e) {
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
            catch (Exception $e) {
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