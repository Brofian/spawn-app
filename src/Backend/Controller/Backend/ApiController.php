<?php declare(strict_types = 1);

namespace SpawnBackend\Controller\Backend;

use SpawnBackend\Exceptions\InvalidEntityNameException;
use SpawnCore\Defaults\Services\ApiResponseBag;
use SpawnCore\System\CardinalSystem\Request;
use SpawnCore\System\Custom\Collection\AssociativeCollection;
use SpawnCore\System\Custom\FoundationStorage\AbstractBackendController;
use SpawnCore\System\Custom\Response\AbstractResponse;
use SpawnCore\System\Custom\Response\CacheControlState;
use SpawnCore\System\Custom\Response\JsonResponse;
use SpawnCore\System\Custom\Response\TwigResponse;
use SpawnCore\System\Database\Criteria\Criteria;
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
     * @route /backend/api/v1/{}/
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

            $apiData = $this->request->getPayloadData();
            if(!$apiData) {
                throw new \RuntimeException('Missing or invalid api body');
            }

            switch ($this->request->getRequestMethod()) {
                case "GET":
                    $apiBag->addData('apiResult', $this->apiSearchAction($repository, $apiData));
                    break;
                case "POST":
                    $apiBag->addData('apiResult',$this->apiUpsertAction($repository, $apiData));
                    break;
                case "DELETE":
                    $apiBag->addData('apiResult',$this->apiDeleteAction($repository, $apiData));
                    break;
                default:
                    $apiBag->addError('Unknown request method');
            }
        }
        catch (\Throwable $throwable) {
            $apiBag->addError($throwable->getMessage());
        }

        return new JsonResponse($apiBag->getResponseData(), CacheControlState::BASE_NOCACHE());
    }

    protected function apiUpsertAction(TableRepository $repository, AssociativeCollection $payload): array {
        /** @var Entity $repositoryEntityClass */
        $repositoryEntityClass = $repository->getEntityClass();

        $attemptedUpsertCount = 0;
        $successFullUpsertCount = 0;
        foreach($payload as $apiEntityData) {
            $attemptedUpsertCount++;
            $entity = $repositoryEntityClass::getEntityFromArray($apiEntityData);
            if($repository->upsert($entity)) {
                $successFullUpsertCount++;
            }
        }

        return [
            'attempted' => $attemptedUpsertCount,
            'successful' => $successFullUpsertCount,
            'hasFailed' => ($attemptedUpsertCount !== $successFullUpsertCount)
        ];
    }

    protected function apiDeleteAction(TableRepository $repository, AssociativeCollection $payload): array {
        return [];
    }

    protected function apiSearchAction(TableRepository $repository, AssociativeCollection $payload): array {

        $criteria = new Criteria();
        // todo adjust criteria by payload
        $limit = $payload->get('limit', 10000);

        $entities = $repository->search($criteria, $limit);

        return array_map(static function(Entity $entity) {
            return $entity->toArray();
        }, $entities->getArray());
    }
}