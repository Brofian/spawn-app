<?php declare(strict_types=1);

namespace SpawnCore\System\Custom\FoundationStorage;


use SpawnCore\System\Custom\Gadgets\TwigHelper;
use SpawnCore\System\ServiceSystem\ServiceContainer;
use SpawnCore\System\ServiceSystem\ServiceContainerProvider;

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