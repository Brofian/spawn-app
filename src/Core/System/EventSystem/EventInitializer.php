<?php declare(strict_types = 1);
namespace SpawnCore\System\EventSystem;

use SpawnCore\System\Custom\Throwables\SubscribeToNotAnEventException;
use SpawnCore\System\ServiceSystem\ServiceContainer;

class EventInitializer
{

    /**
     * @throws SubscribeToNotAnEventException
     */
    public static function registerSubscriberFromServices(ServiceContainer $serviceContainer): void
    {
        $eventEmitter = EventEmitter::get();
        $subscriberServices = $serviceContainer->getServicesByTag('event.subscriber');

        foreach ($subscriberServices as $subscriberService) {
            $subscriberClass = $subscriberService->getClass();

            if (in_array(EventSubscriberInterface::class, class_implements($subscriberClass), true)
            ) {
                /** @var EventSubscriberInterface $subscriberClass */
                $subscriptions = $subscriberClass::getSubscribedEvents();

                foreach ($subscriptions as $eventClass => $listenerDefinition) {

                    if (is_string($listenerDefinition)) {
                        $eventEmitter->subscribe($eventClass, $subscriberService->getId(), $listenerDefinition);
                    } elseif (is_array($listenerDefinition)) {
                        foreach ($listenerDefinition as $listenerMethod) {
                            $eventEmitter->subscribe($eventClass, $subscriberService->getId(), $listenerMethod);
                        }
                    }

                }

            }
        }


    }


}