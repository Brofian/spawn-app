<?php

namespace SpawnBackend\Controller\Backend;


use Exception;
use SpawnBackend\Exceptions\HoneypotException;
use SpawnBackend\Services\AdminLoginManager;
use SpawnCore\System\CardinalSystem\Request;
use SpawnCore\System\Custom\FoundationStorage\AbstractBackendController;
use SpawnCore\System\Custom\Gadgets\CSRFTokenAssistant;
use SpawnCore\System\Custom\Response\AbstractResponse;
use SpawnCore\System\Custom\Response\JsonResponse;
use SpawnCore\System\Custom\Response\TwigResponse;
use SpawnCore\System\ServiceSystem\ServiceContainerProvider;

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