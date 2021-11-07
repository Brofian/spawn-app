<?php

namespace spawnApp\Services;

use spawn\system\Core\Base\EventSystem\Events\RequestRoutedEvent;
use spawn\system\Core\Base\EventSystem\EventSubscriberInterface;
use spawn\system\Core\Services\ServiceContainerProvider;
use spawn\system\Core\Services\ServiceTags;
use spawnApp\Database\AdministratorTable\AdministratorRepository;

class AdminLoginManager implements EventSubscriberInterface {

    public const BACKEND_LOGIN_CONTROLLER_SERVICE_ID = 'system.backend.administrator';
    public const BACKEND_LOGIN_ACTION = 'loginAction';

    protected AdministratorRepository $administratorRepository;

    public function __construct(
        AdministratorRepository $administratorRepository
    )
    {
        $this->administratorRepository = $administratorRepository;
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
        $isLoggedIn = false;

        //if user tries to access a backend page (except login page itself) and is not logged in, change the routing to the backend login
        if(!$isLoggedIn && $isBackend && !$isLoginMethod) {
            $event->setControllerService(ServiceContainerProvider::getServiceContainer()->getService(self::BACKEND_LOGIN_CONTROLLER_SERVICE_ID));
            $event->setMethod(self::BACKEND_LOGIN_ACTION);
        }

    }
}