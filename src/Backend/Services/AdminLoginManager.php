<?php

namespace SpawnBackend\Services;

use DateTime;
use Doctrine\DBAL\Exception;
use SpawnBackend\Database\AdministratorTable\AdministratorEntity;
use SpawnBackend\Exceptions\AdminUserNotFoundException;
use SpawnCore\System\CardinalSystem\Request;
use SpawnCore\System\Custom\Gadgets\SessionHelper;
use SpawnCore\System\Custom\Gadgets\UUID;
use SpawnCore\System\Custom\Throwables\DatabaseConnectionException;
use SpawnCore\System\Custom\Throwables\SubscribeToNotAnEventException;
use SpawnCore\System\Custom\Throwables\WrongEntityForRepositoryException;
use SpawnCore\System\Database\Criteria\Criteria;
use SpawnCore\System\Database\Criteria\Filters\AndFilter;
use SpawnCore\System\Database\Criteria\Filters\EqualsFilter;
use SpawnCore\System\Database\Entity\RepositoryException;
use SpawnCore\System\Database\Entity\TableRepository;
use SpawnCore\System\EventSystem\Events\RequestRoutedEvent;
use SpawnCore\System\EventSystem\EventSubscriberInterface;
use SpawnCore\System\ServiceSystem\ServiceContainerProvider;
use SpawnCore\System\ServiceSystem\ServiceTags;

class AdminLoginManager implements EventSubscriberInterface
{

    public const BACKEND_LOGIN_CONTROLLER_SERVICE_ID = 'system.backend.administrator';
    public const BACKEND_LOGIN_ACTION = 'loginAction';

    protected TableRepository $administratorRepository;
    protected SessionHelper $sessionHelper;

    public function __construct(
        TableRepository $administratorRepository,
        SessionHelper $sessionHelper
    )
    {
        $this->administratorRepository = $administratorRepository;
        $this->sessionHelper = $sessionHelper;
    }

    public static function getSubscribedEvents(): array
    {
        return [];

        return [
            RequestRoutedEvent::class => 'onRequestRoutedEvent'
        ];
    }


    /**
     * @throws DatabaseConnectionException
     * @throws Exception
     * @throws RepositoryException
     * @throws SubscribeToNotAnEventException
     */
    public function onRequestRoutedEvent(RequestRoutedEvent $event): void {
        $isBackend = $event->getControllerService()->hasTag(ServiceTags::BACKEND_CONTROLLER);
        $isLoginMethod = $event->getMethod() === self::BACKEND_LOGIN_ACTION;
        $isLoggedIn = $this->isAdminLoggedIn();

        //dd($isLoggedIn, 'isLoggedIn');

        //if user tries to access a backend page (except login page itself) and is not logged in, change the routing to the backend login
        if(!$isLoggedIn && $isBackend && !$isLoginMethod) {
            $event->setControllerService(ServiceContainerProvider::getServiceContainer()->getService(self::BACKEND_LOGIN_CONTROLLER_SERVICE_ID));
            $event->setMethod(self::BACKEND_LOGIN_ACTION);
        }

    }

    /**
     * @throws AdminUserNotFoundException
     * @throws DatabaseConnectionException
     * @throws RepositoryException
     */
    public function tryAdminLogin(string $username, string $password): ?AdministratorEntity {

        /** @var AdministratorEntity|null $admin */
        $admin = $this->administratorRepository->search(
            new Criteria(new EqualsFilter('username', $username)),
            1
        )->first();

        if($admin === null) {
            throw new AdminUserNotFoundException();
        }


        if(!password_verify($password, $admin->getPassword())) {
            throw new AdminUserNotFoundException();
        }

        try {
            $loginHash = UUID::randomHex();
            $admin->setLoginHash($loginHash);
            $admin->setLoginExpiration((new DateTime())->modify('+1 day'));
            $this->administratorRepository->upsert($admin);
            $this->sessionHelper->set('admin-login-hash', $loginHash);
            $this->sessionHelper->set('admin-login-username', $username);
        }
        catch (\Exception $e) {
            throw new AdminUserNotFoundException();
        }

        return $admin;
    }


    /**
     * @throws DatabaseConnectionException
     * @throws Exception
     * @throws RepositoryException
     * @throws SubscribeToNotAnEventException
     */
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

    /**
     * @throws DatabaseConnectionException
     * @throws Exception
     * @throws RepositoryException
     * @throws SubscribeToNotAnEventException
     */
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

        try {
            /** @var AdministratorEntity|null $admin */
            $admin = $this->administratorRepository->search(
                new Criteria(
                    new AndFilter(
                        new EqualsFilter('username', $adminLoginUsername),
                        new EqualsFilter('loginHash', $adminLoginHash)
                    )
                ),
                1
            )->first();
        }
        catch (\Exception $e) {
            return null;
        }

        if(!$admin instanceof AdministratorEntity) {
            return null;
        }

        $expirationDate = $admin->getLoginExpiration();

        if($expirationDate > new DateTime()) {
            //expirationDate is in the future, so the login hash is still valid
            return $admin;
        }

        return null;
    }

    /**
     * @throws Exception
     * @throws WrongEntityForRepositoryException
     * @throws DatabaseConnectionException
     * @throws RepositoryException
     * @throws SubscribeToNotAnEventException
     */
    public function logoutAdmin(): void {
        $adminEntity = self::getAdminUserFromRequest();

        $this->sessionHelper->destroySession();


        if($adminEntity === null) {
            return;
        }
        $adminEntity->setLoginExpiration(null);
        $adminEntity->setLoginHash(null);

        $this->administratorRepository->upsert($adminEntity);
    }

}