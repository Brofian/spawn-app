<?php declare(strict_types = 1);
namespace SpawnBackend\Controller\Backend;

use Exception;
use SpawnCore\Defaults\Database\SeoUrlTable\SeoUrlEntity;
use SpawnCore\Defaults\Services\SeoUrlManager;
use SpawnCore\System\CardinalSystem\Request;
use SpawnCore\System\Custom\FoundationStorage\AbstractBackendController;
use SpawnCore\System\Custom\Response\AbstractResponse;
use SpawnCore\System\Custom\Response\JsonResponse;
use SpawnCore\System\Custom\Response\TwigResponse;
use SpawnCore\System\Custom\Throwables\DatabaseConnectionException;
use SpawnCore\System\Database\Criteria\Filters\InvalidFilterValueException;
use SpawnCore\System\Database\Entity\RepositoryException;

class SeoUrlConfigController extends AbstractBackendController {

    protected SeoUrlManager $seoUrlManager;
    protected Request $request;

    public function __construct(
        Request $request,
        SeoUrlManager $seoUrlManager
    )
    {
        parent::__construct();
        $this->request = $request;
        $this->seoUrlManager = $seoUrlManager;
    }



    public static function getSidebarMethods(): array
    {
        return [
            'configuration' => [
                'title' => "config",
                'color' => "#00ff00",
                'actions' => [
                    [
                        'route' => 'app.backend.seo_config.overview',
                        'parameters' => [],
                        'title' => 'seo_config'
                    ]
                ]
            ]
        ];
    }

    /**
     * @route /backend/seo_config/overview
     * @name "app.backend.seo_config.overview"
     * @requires admin
     * @locked
     * @throws DatabaseConnectionException
     * @throws RepositoryException
     * @throws \Doctrine\DBAL\Exception
     */
    public function seoUrlOverviewAction(): AbstractResponse {
        $get = $this->request->getGet();

        $numberOfEntriesPerPage = (int)($get->get('num', 20) ?? 1);
        $page = max((int)($get->get('page', 1) ?? 1), 1);
        $ignoreLocked = !($get->get('showLocked', 0));
        $totalNumberOfEntries = ($this->seoUrlManager->getNumberAvailableSeoUrls($ignoreLocked) ?? 1);
        $availablePages = (int)ceil($totalNumberOfEntries / $numberOfEntriesPerPage);
        $seoUrls = $this->seoUrlManager->getSeoUrls($ignoreLocked, $numberOfEntriesPerPage, ($page-1)*$numberOfEntriesPerPage);

        $this->twig->assignBulk([
            'table_info' => [
                'page' => $page,
                'entriesPerPage' => $numberOfEntriesPerPage,
                'availablePages' => $availablePages,
                'showLocked' => ($ignoreLocked ? 0 : 1)
            ],
            'seo_urls' => $seoUrls,
            'content_file' => 'backend/contents/seo_url_config/overview/content.html.twig'
        ]);

        return new TwigResponse('backend/index.html.twig');
    }

    /**
     * @route /backend/seo_config/edit/{}
     * @name "app.backend.seo_config.edit"
     * @requires admin
     * @locked
     */
    public function seoUrlEditAction(string $seoUrlId): AbstractResponse {
        $seoUrl = $this->seoUrlManager->getSeoUrlFromId($seoUrlId);

        $this->twig->assign('seo_url', $seoUrl);
        $this->twig->assign('content_file', 'backend/contents/seo_url_config/edit/content.html.twig');

        return new TwigResponse('backend/index.html.twig');
    }

    /**
     * @route /backend/seo_config/edit/submit/{}/{}
     * @name "app.backend.seo_config.edit.submit"
     * @requires admin
     * @locked
     * @api
     */
    public function seoUrlEditSubmitAction(string $ctrl = null, string $method = null): AbstractResponse {
        /** @var Request $request */
        $data = $this->request->getPost()->getArray();

        $this->validateArrayFields($data, ['cUrl', 'active'], $missingFields , false);

        //return if any field is missing
        if(!empty($missingFields)) {
            return new JsonResponse([
                'success' => false,
                'errors' => ['Missing fields in request'],
                'errorFields' => $missingFields
            ]);
        }

        $errors = [];

        try {
            //load and update any existing seoUrlEntity or create a new one
            /** @var SeoUrlEntity|null $existingSeoUrl */
            $seoUrlEntity = $this->seoUrlManager->getSeoUrl($ctrl, $method);

            if($seoUrlEntity instanceof SeoUrlEntity) {
                $seoUrlEntity->setCUrl($data['cUrl']);
                $seoUrlEntity->setActive($data['active']==='true');
            }
            else {
                $seoUrlEntity = new SeoUrlEntity($data['cUrl'], $ctrl, $method, [], false, $data['active']==='true');
            }

            $this->seoUrlManager->saveSeoUrlEntity($seoUrlEntity);
        }
        catch(Exception $e) {
            $errors[] = $e->getMessage();
        }


        return new JsonResponse([
            'success' => empty($errors),
            'errors' => $errors
        ]);
    }



    protected function validateArrayFields(array $array, array $requiredFields, &$missingFields, bool $allowFalseValues = false): bool {
        $missingFields = [];
        $arrayKeys = array_keys($array);

        foreach($requiredFields as $requiredField) {
            if(!in_array($requiredField, $arrayKeys, true)) {
                //if required field does not exist in array
                $missingFields[] = $requiredField;
            }
            elseif(!$allowFalseValues && !$array[$requiredField]) {
                //if falsy values are not allowed, but the field has a value, that can be evaluated to false
                $missingFields[] = $requiredField;
            }
        }

        return empty($missingFields);
    }


}