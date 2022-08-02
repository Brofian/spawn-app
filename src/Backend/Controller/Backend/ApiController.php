<?php declare(strict_types = 1);

namespace SpawnBackend\Controller\Backend;

use SpawnBackend\Exceptions\InvalidEntityNameException;
use SpawnCore\Defaults\Services\ApiResponseBag;
use SpawnCore\System\CardinalSystem\Request;
use SpawnCore\System\Custom\FoundationStorage\AbstractBackendController;
use SpawnCore\System\Custom\Response\AbstractResponse;
use SpawnCore\System\Custom\Response\CacheControlState;
use SpawnCore\System\Custom\Response\JsonResponse;
use SpawnCore\System\Custom\Response\TwigResponse;
use SpawnCore\System\Database\Entity\Entity;
use SpawnCore\System\Database\Entity\TableRepository;

class ApiController extends AbstractBackendController {

    protected Request $request;

    public function __construct(
        Request $request
    )
    {
        $this->request = $request;
        parent::__construct();
    }

    public static function getSidebarMethods(): array
    {
        return [
            //Category
            'configuration' => [
                'title' => "config",
                'color' => "#00ff00",
                'actions' => [
                    [
                        'route' => 'app.backend.api.v1.overview',
                        'parameters' => [],
                        'title' => 'api_overview'
                    ]
                ]
            ]
        ];
    }

    /**
     * @route /backend/api/v1/overview/
     * @name "app.backend.api.v1.overview"
     * @requires admin
     * @locked
     * @return AbstractResponse
     */
    public function apiOverviewAction(): AbstractResponse {

        return new TwigResponse('backend/contents/api/overview.html.twig', [

        ]);
    }

    /**
     * @route /backend/api/v1/{entityName}/
     * @name "app.backend.api.v1"
     * @requires admin
     * @locked
     * @api
     * @return AbstractResponse
     */
    public function apiAction(string $entityName): AbstractResponse {

        $apiBag = new ApiResponseBag();
        try {
            /** @var TableRepository $repository */
            $repository = $this->container->getServiceInstance($entityName.'.repository');
            if(!$repository instanceof TableRepository) {
                throw new InvalidEntityNameException($entityName);
            }

            $apiData = $this->request->getPost()->getArray();
            if(!$apiData) {
                throw new \RuntimeException('Missing or invalid api body');
            }

            /** @var Entity $repositoryEntityClass */
            $repositoryEntityClass = $repository->getEntityClass();

            foreach($apiData as $apiEntityData) {
                $entity = $repositoryEntityClass::getEntityFromArray($apiEntityData);
                $repository->upsert($entity);
            }
        }
        catch (\Throwable $throwable) {
            $apiBag->addError($throwable->getMessage());
        }

        return new JsonResponse($apiBag->getResponseData(), CacheControlState::BASE_NOCACHE());
    }
}