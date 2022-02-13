<?php declare(strict_types=1);

namespace SpawnCore\System\ServiceSystem;


class ServiceContainer
{

    /** @var Service[] */
    protected array $services = array();
    /** @var String[] */
    protected array $decorations = array();


    public function addService(Service $service): self
    {
        $this->services[$service->getId()] = $service->setServiceContainer($this);
        return $this;
    }

    public function defineService(
        string $id,
        ?string $class = null,
        array $tags = [],
        bool $static = false,
        bool $abstract = false,
        ?string $decorates = null,
        ?string $parent = null,
        ?int $moduleId = null,
        $instance = null
    ): void
    {

        $service = new Service();
        $service->setId($id);
        if ($class) $service->setClass($class);
        if ($moduleId) $service->setModuleId($moduleId);
        if ($tags) $service->setTags($tags);
        if ($abstract) $service->setAbstract($abstract);
        if ($static) $service->setStatic($static);
        if ($decorates) $service->setDecorates($decorates);
        if ($parent) $service->setParent($parent);
        if ($instance) $service->setInstance($instance);

        $this->services[$service->getId()] = $service->setServiceContainer($this);
    }

    /** @return mixed */
    public function get(string $key)
    {
        return $this->getServiceInstance($key);
    }

    /** @return mixed */
    public function getServiceInstance(string $key)
    {

        $service = $this->getService($key);

        if ($service) {
            return $this->services[$key]->getInstance();
        }

        return null;
    }

    public function getService(string $key): ?Service
    {

        //check if this key is decorated -> query and return the decorating service
        if (isset($this->decorations[$key])) {
            return $this->getService($this->decorations[$key]);
        }

        //check if this service is registered
        if (isset($this->services[$key])) {
            return $this->services[$key];
        } else {
            return null;
        }
    }

    /** @return Service[] */
    public function getServicesByTag(string $tag): array
    {
        $services = [];

        foreach ($this->services as $service) {
            if ($service->hasTag($tag)) {
                $services[$service->getId()] = $service;
            }
        }

        return $services;
    }

    /** @return Service[] */
    public function getServicesByTags(array $tags): array
    {
        $services = [];

        foreach ($this->services as $service) {
            foreach ($tags as $tag) {
                if ($service->hasTag($tag)) {
                    $services[$service->getId()] = $service;
                    break;
                }
            }
        }

        return $services;
    }

    /** @return Service[] */
    public function getServices(): ?array
    {
        return $this->services;
    }


    public function updateDecorations(): void
    {
        $this->decorations = [];

        foreach ($this->services as $service) {
            $decoratedServiceId = $service->getDecorates();

            if ($decoratedServiceId) {
                $decoratedService = $this->getService($decoratedServiceId);

                if ($decoratedService) {
                    $this->decorations[$decoratedService->getId()] = $service->getId();
                }
            }
        }
    }

}