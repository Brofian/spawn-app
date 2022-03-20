<?php

namespace SpawnCore\System\CardinalSystem;

use Doctrine\DBAL\Exception;
use SpawnCore\System\Custom\Response\AbstractResponse;
use SpawnCore\System\Custom\Response\JsonResponse;
use SpawnCore\System\Custom\Response\SimpleResponse;
use SpawnCore\System\Custom\Throwables\DatabaseConnectionException;
use SpawnCore\System\Custom\Throwables\NoActionFoundInControllerException;
use SpawnCore\System\Custom\Throwables\NoControllerFoundException;
use SpawnCore\System\Custom\Throwables\SubscribeToNotAnEventException;
use SpawnCore\System\Database\Entity\RepositoryException;
use SpawnCore\System\EventSystem\EventEmitter;
use SpawnCore\System\EventSystem\Events\RequestRoutedEvent;
use SpawnCore\System\NavigationSystem\Navigator;
use SpawnCore\System\ServiceSystem\Service;
use SpawnCore\System\ServiceSystem\ServiceContainer;
use SpawnCore\System\ServiceSystem\ServiceContainerProvider;

class RequestHandler
{

    protected ServiceContainer $serviceContainer;
    protected ?Service $controllerService;
    protected ?string $actionMethod;
    protected array $cUrlValues;


    /**
     * @throws DatabaseConnectionException
     * @throws Exception
     * @throws RepositoryException
     * @throws SubscribeToNotAnEventException
     */
    public function __construct()
    {
        $this->serviceContainer = ServiceContainerProvider::getServiceContainer();
    }

    /**
     * @throws DatabaseConnectionException
     * @throws Exception
     * @throws NoActionFoundInControllerException
     * @throws NoControllerFoundException
     * @throws RepositoryException
     */
    public function handleRequest(): void
    {
        $this->findRouting();
        $this->callControllerMethod();
    }


    /**
     * @throws NoActionFoundInControllerException
     * @throws NoControllerFoundException
     * @throws Exception
     * @throws DatabaseConnectionException
     * @throws RepositoryException
     */
    protected function findRouting(): void
    {
        /** @var Navigator $routingHelper */
        $routingHelper = $this->serviceContainer->getServiceInstance('system.routing.helper');
        /** @var Request $request */
        $request = $this->serviceContainer->getServiceInstance('system.kernel.request');
        $this->cUrlValues = $request->getCurlValues();
        $getBag = $request->getGet();

        $routingHelper->route(
            $getBag->get('controller') ?? "",
            $getBag->get('action') ?? "",
            $this->controllerService,
            $this->actionMethod
        );

        $event = new RequestRoutedEvent($request, $this->controllerService, $this->actionMethod);
        EventEmitter::get()->publish($event);
        $this->controllerService = $event->getControllerService();
        $this->actionMethod = $event->getMethod();

        if (!$this->controllerService) {
            throw new NoControllerFoundException($getBag->get('controller'));
        }

        if (!$this->actionMethod) {
            throw new NoActionFoundInControllerException($getBag->get('controller'), $getBag->get('action'));
        }
    }

    protected function callControllerMethod(): void
    {
        $controllerInstance = $this->controllerService->getInstance();
        $actionMethod = $this->actionMethod;

        $responseObject = $controllerInstance->$actionMethod(...array_values($this->cUrlValues));
        $responseObject = $this->validateAndCovertResponseObject($responseObject);

        /** @var Response $response */
        $response = $this->serviceContainer->getServiceInstance('system.kernel.response');
        $response->setResponseObject($responseObject);
    }

    /**
     * @param $responseObject
     * @return AbstractResponse
     */
    protected function validateAndCovertResponseObject($responseObject): AbstractResponse
    {
        if ($responseObject instanceof AbstractResponse) {
            return $responseObject;
        }

        if (is_string($responseObject) || is_numeric($responseObject)) {
            return new SimpleResponse((string)$responseObject);
        }

        if (is_array($responseObject)) {
            return new JsonResponse($responseObject);
        }

        return new SimpleResponse('Could not parse Controller Result to Response Object');
    }
}