<?php

namespace SpawnCore\System\CardinalSystem;

use Doctrine\DBAL\Exception;
use SpawnCore\Defaults\Services\UserManager;
use SpawnCore\System\CardinalSystem\ModuleNetwork\ModuleLoader;
use SpawnCore\System\Custom\Throwables\DatabaseConnectionException;
use SpawnCore\System\Custom\Throwables\HeadersSendByException;
use SpawnCore\System\Custom\Throwables\NoActionFoundInControllerException;
use SpawnCore\System\Custom\Throwables\NoControllerFoundException;
use SpawnCore\System\Custom\Throwables\SubscribeToNotAnEventException;
use SpawnCore\System\Database\Entity\EntityCollection;
use SpawnCore\System\Database\Entity\RepositoryException;
use SpawnCore\System\ServiceSystem\ServiceContainer;
use SpawnCore\System\ServiceSystem\ServiceContainerProvider;
use SpawnCore\System\ServiceSystem\ServiceTags;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class Kernel
{

    protected Request $request;
    protected Response $response;
    protected EntityCollection $moduleCollection;

    /**
     * @throws Exception
     * @throws DatabaseConnectionException
     * @throws RepositoryException
     */
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


        /** @var UserManager $userManager */
        $userManager = $serviceContainer->get('system.service.user_manager');
        $this->request->setUser($userManager->getCurrentlyLoggedInUser());

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
     * @throws DatabaseConnectionException
     * @throws Exception
     * @throws RepositoryException
     */
    public function handle(): void
    {
        $requestHandler = new RequestHandler();
        $requestHandler->handleRequest();
    }

    /**
     * @return string
     * @throws DatabaseConnectionException
     * @throws Exception
     * @throws HeadersSendByException
     * @throws LoaderError
     * @throws RepositoryException
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function getAnswer(): string
    {
        $this->response->prepareFiles();

        return $this->response->finish();
    }


}