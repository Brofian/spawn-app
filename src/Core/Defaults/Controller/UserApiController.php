<?php declare(strict_types=1);

namespace SpawnCore\Defaults\Controller;


use SpawnCore\Defaults\Database\UserTable\UserEntity;
use SpawnCore\Defaults\Services\ApiResponseBag;
use SpawnCore\Defaults\Services\UserManager;
use SpawnCore\System\CardinalSystem\Request;
use SpawnCore\System\Custom\FoundationStorage\AbstractController;
use SpawnCore\System\Custom\Gadgets\SessionHelper;
use SpawnCore\System\Custom\Response\AbstractResponse;
use SpawnCore\System\Custom\Response\CacheControlState;
use SpawnCore\System\Custom\Response\JsonResponse;

class UserApiController extends AbstractController {

    protected UserManager $userManager;
    protected Request $request;
    protected SessionHelper $sessionHelper;

    public function __construct(
        UserManager $userManager,
        Request $request,
        SessionHelper $sessionHelper
    )   {
        $this->userManager = $userManager;
        $this->request = $request;
        $this->sessionHelper = $sessionHelper;
        parent::__construct();
    }


    /**
     * @route /api/user/login
     * @name "app.api.user.login"
     * @api
     * @locked
     * @return AbstractResponse
     */
    public function loginAction(): AbstractResponse {
        $responseBag = new ApiResponseBag();
        try {
            $username = $this->request->getPost()->get('login_username');
            $password = $this->request->getPost()->get('login_password');

            if(!$username) {
                throw new \RuntimeException('Missing username: ' . var_export($this->request->getPost()->getArray(), true));
            }
            if(!$password) {
                throw new \RuntimeException('Missing password');
            }

            $isAppRequest = $this->request->getPost()->get('login_type_app', false);
            $user = $this->userManager->tryLogin($username, $password);
            if($user instanceof UserEntity) {
                if(!$user->getLoginHash()) {
                    $newToken = md5($this->request->getClientIp() . $username . time());
                    $user->setLoginHash($newToken);
                    $this->userManager->upsertUser($user);
                }

                if($isAppRequest) {
                    $responseBag->addData(UserManager::USER_LOGIN_TOKEN, $user->getLoginHash());
                }
                $this->sessionHelper->set(UserManager::USER_LOGIN_TOKEN, $user->getLoginHash());
            }
            else {
                $responseBag->addError('Invalid credentials!', true);
            }
        }
        catch (\Exception $e) {
            $responseBag->addError($e->getMessage(), false);
        }

        return new JsonResponse($responseBag->getResponseData(), new CacheControlState(false, true, true));
    }


    /**
     * @route /api/user/validate
     * @name "app.api.user.validate"
     * @api
     * @locked
     * @return AbstractResponse
     */
    public function validateAction(): AbstractResponse {
        $responseBag = new ApiResponseBag();
        try {
            $currentUser = $this->userManager->getCurrentlyLoggedInUser(true);
            $responseBag->addData('isValidLoginSession', $currentUser !== null);
        }
        catch (\Exception $e) {
            $responseBag->addError(MODE==='dev' ? $e->getMessage() : 'Something went wrong!');
        }

        return new JsonResponse($responseBag->getResponseData(), new CacheControlState(false, true, true));
    }



    /**
     * @route /api/user/logout
     * @name "app.api.user.logout"
     * @api
     * @locked
     * @return AbstractResponse
     */
    public function logoutAction(): AbstractResponse {
        $responseBag = new ApiResponseBag();

        try {
            if(!$this->request->getUser()) {
                throw new \RuntimeException('User is already not logged in!');
            }

            $this->userManager->
            $this->request->setUser(null);

        }
        catch (\Throwable $t) {
            $responseBag->addError($t->getMessage());
        }

        return new JsonResponse($responseBag->getResponseData(), new CacheControlState(false, true, true));
    }

}