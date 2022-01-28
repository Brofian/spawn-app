<?php

namespace spawnCore\Custom\Response;

use spawnCore\NavigationSystem\Navigator;
use spawnCore\ServiceSystem\ServiceContainerProvider;

/**
 * If this is returned by an action inside of a controller, the user will be redirected to the given controller,
 * instead of receiving the text response
 */
class RedirectResponse extends AbstractResponse
{

    public function __construct(string $controller, string $method, array $parameters = [])
    {
        parent::__construct('', new CacheControlState(true, true));

        /** @var Navigator $routingHelper */
        $routingHelper = ServiceContainerProvider::getServiceContainer()->getServiceInstance('system.routing.helper');
        $newLink = $routingHelper->getSeoLinkByParameters($controller, $method, $parameters);

        header('Location: ' . $newLink);
    }

}