<?php

namespace spawnCore\CardinalSystem;

use spawnCore\CardinalSystem\ModuleNetwork\ModuleLoader;
use spawnCore\Custom\Throwables\NoActionFoundInControllerException;
use spawnCore\Custom\Throwables\NoControllerFoundException;
use spawnCore\Database\Entity\EntityCollection;
use spawnCore\ServiceSystem\ServiceContainer;
use spawnCore\ServiceSystem\ServiceContainerProvider;
use spawnCore\ServiceSystem\ServiceTags;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class Kernel
{

    protected Request $request;
    protected Response $response;
    protected EntityCollection $moduleCollection;

    public function __construct()
    {

        $moduleLoader = new ModuleLoader();
        $this->moduleCollection = $moduleLoader->loadModules();

        $serviceContainer = ServiceContainerProvider::getServiceContainer();

        $this->defineModuleCollection($serviceContainer);
        $this->defineRequest($serviceContainer);
        $this->defineResponse($serviceContainer);
    }

    protected function defineModuleCollection(ServiceContainer $serviceContainer): EntityCollection
    {
        $serviceContainer->defineService(
            'system.modules.collection',
            EntityCollection::class,
            [ServiceTags::BASE_SERVICE],
            true,
            false,
            null,
            null,
            null,
            $this->moduleCollection,
            );
        return $this->moduleCollection;
    }

    protected function defineRequest(ServiceContainer $serviceContainer): Request
    {
        $this->request = new Request();
        $serviceContainer->defineService(
            'system.kernel.request',
            Request::class,
            [ServiceTags::BASE_SERVICE],
            true,
            false,
            null,
            null,
            null,
            $this->request,
            );
        return $this->request;
    }

    protected function defineResponse(ServiceContainer $serviceContainer): Response
    {
        $this->response = new Response();
        $serviceContainer->defineService(
            'system.kernel.response',
            Response::class,
            [ServiceTags::BASE_SERVICE],
            true,
            false,
            null,
            null,
            null,
            $this->response,
            );
        return $this->response;
    }

    /**
     * @throws NoActionFoundInControllerException
     * @throws NoControllerFoundException
     */
    public function handle(): void
    {
        $requestHandler = new RequestHandler();
        $requestHandler->handleRequest();
    }

    /**
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function getAnswer(): string
    {
        $this->response->prepareFiles();

        return $this->response->finish();
    }


}