<?php

namespace SpawnCore\System\EventSystem\Events;

use SpawnCore\System\CardinalSystem\Request;
use SpawnCore\System\EventSystem\Event;
use SpawnCore\System\ServiceSystem\Service;
use SpawnCore\System\ServiceSystem\ServiceTags;

class RequestRoutedEvent extends Event
{

    protected Request $request;
    protected Service $controllerService;
    protected string $method;

    public function __construct(
        Request $request,
        Service $controllerService,
        string $method
    )
    {
        $this->request = $request;
        $this->controllerService = $controllerService;
        $this->method = $method;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function getControllerService(): Service
    {
        return $this->controllerService;
    }

    public function setControllerService(Service $service): void
    {
        if ($service->hasTag(ServiceTags::BASE_CONTROLLER) || $service->hasTag(ServiceTags::BACKEND_CONTROLLER)) {
            $this->controllerService = $service;
        }
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function setMethod(string $method): void
    {
        $this->method = $method;
    }

}