<?php


namespace spawnApp\Controller\Backend;

use spawn\system\Core\Base\Controller\AbstractBackendController;
use spawn\system\Core\Base\Database\Definition\Entity;
use spawn\system\Core\Base\Database\Definition\EntityCollection;
use spawn\system\Core\Contents\Response\AbstractResponse;
use spawn\system\Core\Contents\Response\JsonResponse;
use spawn\system\Core\Contents\Response\RedirectResponse;
use spawn\system\Core\Contents\Response\SimpleResponse;
use spawn\system\Core\Contents\Response\TwigResponse;
use spawn\system\Core\Custom\ClassInspector;
use spawn\system\Core\Request;
use spawn\system\Core\Services\Service;
use spawn\system\Core\Services\ServiceTags;
use spawnApp\Database\SeoUrlTable\SeoUrlEntity;
use spawnApp\Database\SeoUrlTable\SeoUrlRepository;
use spawnApp\Services\SeoUrlManager;

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
                'title' => "Einstellungen", //Kategory title
                'color' => "#00ff00", //Kategory color
                'actions' => [
                    [
                        'controller' => '%self.key%',
                        'action' => 'seoUrlOverviewAction',
                        'title' => 'SEO URLs'
                    ],
                    [
                        'controller' => '%self.key%',
                        'action' => 'seoUrlOverviewAction',
                        'title' => 'SEO URLs'
                    ]
                ]
            ]
        ];
    }

    /**
     * @route /backend/seo_config/overview
     * @locked
     * @return AbstractResponse
     */
    public function seoUrlOverviewAction(): AbstractResponse {
        $seoUrls = $this->seoUrlManager->getSeoUrls();

        $this->twig->assign('seo_urls', $seoUrls);
        $this->twig->assign('content_file', 'backend/contents/seo_url_config/overview/content.html.twig');

        return new TwigResponse('backend/index.html.twig');
    }

    /**
     * @route /backend/seo_config/edit/{}/{}
     * @locked
     * @param string|null $ctrl
     * @param string|null $method
     * @return AbstractResponse
     */
    public function seoUrlEditAction(string $ctrl, string $method): AbstractResponse {

        $seoUrl = $this->seoUrlManager->getSeoUrl($ctrl, $method);

        $this->twig->assign('seo_url', $seoUrl);
        $this->twig->assign('content_file', 'backend/contents/seo_url_config/edit/content.html.twig');

        return new TwigResponse('backend/index.html.twig');
    }

    /**
     * @route /backend/seo_config/edit/submit/{}/{}
     * @locked
     * @param string|null $ctrl
     * @param $method
     * @return AbstractResponse
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
        catch(\Exception $e) {
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
            if(!in_array($requiredField, $arrayKeys)) {
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