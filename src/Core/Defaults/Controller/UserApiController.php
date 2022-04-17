<?php declare(strict_types=1);

namespace SpawnCore\Defaults\Commands;


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
                $responseBag->addError('Invalid credentials!');
            }
        }
        catch (\Exception $e) {
            $responseBag->addError(MODE==='dev' ? $e->getMessage() : 'Something went wrong!');
        }

        return new JsonResponse($responseBag->getResponseData(), new CacheControlState(false, true, true));
    }



}