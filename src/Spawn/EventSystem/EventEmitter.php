<?php

namespace spawnCore\EventSystem;


use spawnCore\ServiceSystem\ServiceContainerProvider;

class EventEmitter
{

    protected static EventEmitter $instance;
    protected array $eventListeners = [];

    public static function get(): EventEmitter
    {
        if (!isset(self::$instance)) {
            self::$instance = new EventEmitter();
        }
        return self::$instance;
    }

    /**
     * @param string $eventClass
     * @param string $serviceId
     * @param string $action
     * @throws SubscribeToNotAnEventException
     */
    public function subscribe(string $eventClass, string $serviceId, string $action)
    {
        if (!is_subclass_of($eventClass, Event::class)) {
            throw new SubscribeToNotAnEventException($eventClass);
        }

        $serviceAlreadyRegistered = isset($this->eventListeners[$eventClass][$serviceId]);

        if (!$serviceAlreadyRegistered ||
            ($serviceAlreadyRegistered && !in_array($action, $this->eventListeners[$eventClass][$serviceId]))) {
            $this->eventListeners[$eventClass][$serviceId] = [];
        }

        $this->eventListeners[$eventClass][$serviceId][] = $action;
    }

    /**
     * @param Event $event
     */
    public function publish(Event $event)
    {
        $eventClass = get_class($event);
        $serviceContainer = ServiceContainerProvider::getServiceContainer();

        foreach ($this->eventListeners as $listenerEventClass => $whatever) {
            if (!is_subclass_of($eventClass, $listenerEventClass) && !$eventClass == $listenerEventClass) {
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