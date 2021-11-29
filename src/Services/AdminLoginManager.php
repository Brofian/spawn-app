<?php

namespace spawnApp\Services;

use Doctrine\DBAL\Exception;
use spawn\system\Core\Base\EventSystem\Events\RequestRoutedEvent;
use spawn\system\Core\Base\EventSystem\EventSubscriberInterface;
use spawn\system\Core\Helper\SessionHelper;
use spawn\system\Core\Helper\UUID;
use spawn\system\Core\Request;
use spawn\system\Core\Services\ServiceContainerProvider;
use spawn\system\Core\Services\ServiceTags;
use spawn\system\Throwables\WrongEntityForRepositoryException;
use spawnApp\Controller\Backend\Exceptions\AdminUserNotFoundException;
use spawnApp\Database\AdministratorTable\AdministratorEntity;
use spawnApp\Database\AdministratorTable\AdministratorRepository;

class AdminLoginManager implements EventSubscriberInterface {

    public const BACKEND_LOGIN_CONTROLLER_SERVICE_ID = 'system.backend.administrator';
    public const BACKEND_LOGIN_ACTION = 'loginAction';

    protected AdministratorRepository $administratorRepository;
    protected SessionHelper $sessionHelper;

    public function __construct(
        AdministratorRepository $administratorRepository,
        SessionHelper $sessionHelper
    )
    {
        $this->administratorRepository = $administratorRepository;
        $this->sessionHelper = $sessionHelper;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RequestRoutedEvent::class => 'onRequestRoutedEvent'
        ];
    }


    public function onRequestRoutedEvent(RequestRoutedEvent $event) {
        $isBackend = $event->getControllerService()->hasTag(ServiceTags::BACKEND_CONTROLLER);
        $isLoginMethod = $event->getMethod() === self::BACKEND_LOGIN_ACTION;
        $isLoggedIn = self::isAdminLoggedIn();

        //dd($isLoggedIn, 'isLoggedIn');

        //if user tries to access a backend page (except login page itself) and is not logged in, change the routing to the backend login
        if(!$isLoggedIn && $isBackend && !$isLoginMethod) {
            $event->setControllerService(ServiceContainerProvider::getServiceContainer()->getService(self::BACKEND_LOGIN_CONTROLLER_SERVICE_ID));
            $event->setMethod(self::BACKEND_LOGIN_ACTION);
        }

    }

    /**
     * @param string $username
     * @param string $password
     * @return AdministratorEntity|null
     * @throws AdminUserNotFoundException
     */
    public function tryAdminLogin(string $username, string $password): ?AdministratorEntity {

        /** @var AdministratorEntity|null $admin */
        $admin = $this->administratorRepository->search([
            'username' => $username
        ], 1)->first();

        if($admin === null) {
            throw new AdminUserNotFoundException();
        }


        if(!password_verify($password, $admin->getPassword())) {
            throw new AdminUserNotFoundException();
        }

        try {
            $loginHash = UUID::randomHex();
            $admin->setLoginHash($loginHash);
            $admin->setLoginExpiration((new \DateTime())->modify('+1 day'));
            $this->administratorRepository->upsert($admin);
            $this->sessionHelper->set('admin-login-hash', $loginHash);
            $this->sessionHelper->set('admin-login-username', $username);
        }
        catch (\Exception $e) {
            throw new AdminUserNotFoundException();
        }

        return $admin;
    }


    public function isAdminLoggedIn(): bool
    {
        /** @var Request $request */
        $request = ServiceContainerProvider::getServiceContainer()->getServiceInstance('system.kernel.request');

        if(self::getAdminUserFromRequest($request) !== null) {
            return true;
        }

        $sessionAdmin = $this->getAdminUserFromSessionData();
        if($sessionAdmin instanceof AdministratorEntity) {
            self::setAdminUserToRequest($request, $sessionAdmin);
            return true;
        }

        return false;
    }


    public static function setAdminUserToRequest(Request $request, AdministratorEntity $admin): void {
        $request->set('admin-user', $admin);
    }

    public static function getAdminUserFromRequest(Request $request = null): ?AdministratorEntity {
        if($request === null) {
            /** @var Request $request */
            $request = ServiceContainerProvider::getServiceContainer()->getServiceInstance('system.kernel.request');
        }

        $adminUser = $request->get('admin-user');
        if($adminUser instanceof AdministratorEntity) {
            return $adminUser;
        }
        return null;
    }

    public function getAdminUserFromSessionData(): ?AdministratorEntity {
        $adminLoginHash = $this->sessionHelper->get('admin-login-hash', null);
        $adminLoginUsername = $this->sessionHelper->get('admin-login-username', null);

        if($adminLoginHash === null || $adminLoginUsername === null) {
            return null;
        }

        /** @var AdministratorEntity|null $admin */
        $admin = $this->administratorRepository->search([
            'username' => $adminLoginUsername,
            'loginHash' => $adminLoginHash
        ], 1)->first();

        if(!$admin instanceof AdministratorEntity) {
            return null;
        }

        $expirationDate = $admin->getLoginExpiration();

        if($expirationDate > new \DateTime()) {
            //expirationDate is in the future, so the login hash is still valid
            return $admin;
        }

        return null;
    }

    /**
     * @throws Exception
     * @throws WrongEntityForRepositoryException
     */
    public function logoutAdmin(): void {
        $adminEntity = self::getAdminUserFromRequest();

        $this->sessionHelper->destroySession();
        $adminEntity->setLoginExpiration(null);
        $adminEntity->setLoginHash(null);

        $this->administratorRepository->upsert($adminEntity);
    }

}