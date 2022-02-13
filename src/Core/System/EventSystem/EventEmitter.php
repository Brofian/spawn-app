<?php

namespace SpawnCore\System\EventSystem;


use Doctrine\DBAL\Exception;
use SpawnCore\System\Custom\Throwables\DatabaseConnectionException;
use SpawnCore\System\Custom\Throwables\SubscribeToNotAnEventException;
use SpawnCore\System\Database\Entity\RepositoryException;
use SpawnCore\System\ServiceSystem\ServiceContainerProvider;

class EventEmitter
{

    protected static EventEmitter $instance;
    protected array $eventListeners = [];

    public static function get(): EventEmitter
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * @param string $eventClass
     * @param string $serviceId
     * @param string $action
     * @throws SubscribeToNotAnEventException
     */
    public function subscribe(string $eventClass, string $serviceId, string $action): void
    {
        if (!is_subclass_of($eventClass, Event::class)) {
            throw new SubscribeToNotAnEventException($eventClass);
        }

        $serviceAlreadyRegistered = isset($this->eventListeners[$eventClass][$serviceId]);

        if (!$serviceAlreadyRegistered ||
            ($serviceAlreadyRegistered && !in_array($action, $this->eventListeners[$eventClass][$serviceId], true))) {
            $this->eventListeners[$eventClass][$serviceId] = [];
        }

        $this->eventListeners[$eventClass][$serviceId][] = $action;
    }

    /**
     * @param Event $event
     * @throws Exception
     * @throws DatabaseConnectionException
     * @throws RepositoryException
     * @throws SubscribeToNotAnEventException
     */
    public function publish(Event $event): void
    {
        $eventClass = get_class($event);
        $serviceContainer = ServiceContainerProvider::getServiceContainer();

        foreach ($this->eventListeners as $listenerEventClass => $whatever) {
            if (!is_subclass_of($eventClass, $listenerEventClass) && $eventClass !== $listenerEventClass) {
                continue;
            }


            //if this event has any subscribers
            if (isset($this->eventListeners[$listenerEventClass])) {
                //for every service with subscriptions
                foreach ($this->eventListeners[$listenerEventClass] as $serviceId => $actions) {
                    // for every subscribed action
                    foreach ($actions as $action) {

                        //get the instance of this service, and call the action!
                        $service = $serviceContainer->getServiceInstance($serviceId);
                        $service->{$action}($event);
                    }
                }
            }

        }


    }


}