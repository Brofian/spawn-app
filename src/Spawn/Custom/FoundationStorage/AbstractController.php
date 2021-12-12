<?php declare(strict_types=1);

namespace spawnCore\Custom\FoundationStorage;


use spawnCore\Custom\Gadgets\TwigHelper;
use spawnCore\ServiceSystem\ServiceContainer;
use spawnCore\ServiceSystem\ServiceContainerProvider;

abstract class AbstractController
{

    protected ServiceContainer $container;
    protected TwigHelper $twig;

    public function __construct()
    {
        $this->container = ServiceContainerProvider::getServiceContainer();
        $this->twig = $this->container->getServiceInstance('system.twig.helper');
    }

}