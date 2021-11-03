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
use spawn\system\Core\Request;
use spawn\system\Core\Services\Service;
use spawn\system\Core\Services\ServiceTags;
use spawnApp\Database\SeoUrlTable\SeoUrlEntity;
use spawnApp\Database\SeoUrlTable\SeoUrlRepository;

class SeoUrlConfigController extends AbstractBackendController {

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
                    ]
                ]
            ]
        ];
    }


    public function seoUrlOverviewAction() {

        /** @var SeoUrlRepository $seoUrlRepository */
        $seoUrlRepository = $this->container->get('system.repository.seo_urls');
        $seoUrls = $seoUrlRepository->search();

        $this->twig->assign('seo_urls', $this->getAvailableControllerActions($seoUrls));
        $this->twig->assign('content_file', 'backend/contents/seo_url_config/overview/content.html.twig');

        return new TwigResponse('backend/index.html.twig');
    }


    public function seoUrlEditAction(string $ctrl = null, string $method = null) {

        $seoUrlRepository = $this->container->get('system.repository.seo_urls');
        $seoUrls = $seoUrlRepository->search([
            'controller' => $ctrl,
            'action' => $method
        ]);

        $controllerService = $this->container->getService($ctrl);

        $seoUrlData = null;
        if($controllerService instanceof Service) {
            $seoUrlArrays = $this->getControllerActionsForService($controllerService, $seoUrls);

            foreach($seoUrlArrays as $seoUrlArray) {
                if($seoUrlArray['method'] == $method) {
                    $seoUrlData = $seoUrlArray;
                }
            }
        }

        $this->twig->assign('seo_url', $seoUrlData);
        $this->twig->assign('content_file', 'backend/contents/seo_url_config/edit/content.html.twig');

        return new TwigResponse('backend/index.html.twig');
    }


    public function seoUrlEditSubmitAction(string $ctrl = null, string $method = null): AbstractResponse {
        /** @var Request $request */
        $request = $this->container->get('system.kernel.request');

        $errors = [
            'This is an error'
        ];

        return new JsonResponse([
            'success' => empty($errors),
            'errors' => $errors
        ]);
    }


    protected function getAvailableControllerActions(EntityCollection $registeredSeoUrls): array {
        //load available controller action combinations
        $controllerServices = $this->container->getServicesByTags(
            [
                ServiceTags::BASE_CONTROLLER,
                ServiceTags::BACKEND_CONTROLLER,
            ]
        );


        $actions = [];
        foreach($controllerServices as $controllerService) {

            $action = $this->getControllerActionsForService($controllerService, $registeredSeoUrls);

            if(!empty($action)) {
                $actions = array_merge($actions, $action);
            }

        }


        return $actions;
    }

    protected function getControllerActionsForService(Service $controllerService, EntityCollection $registeredSeoUrls): array {
        $actions = [];

        try {
            $class = new \ReflectionClass($controllerService->getClass());
            $methods = $class->getMethods(\ReflectionMethod::IS_PUBLIC);

            /** @var \ReflectionMethod $method */
            foreach($methods as $method) {
                if(strpos($method->getName(), '__') !== 0 && preg_match('/^.*Action$/m', $method->getName())) {

                    $action = $method->getName();
                    $controller = $controllerService->getId();
                    $params = $method->getParameters();
                    $seo_url = null;

                    /** @var SeoUrlEntity $registeredSeoUrl */
                    foreach($registeredSeoUrls as $registeredSeoUrl) {
                        if($registeredSeoUrl->getController() == $controller && $registeredSeoUrl->getAction() == $action) {
                            $seo_url = $registeredSeoUrl;
                            break;
                        }
                    }

                    $actions[] = [
                        'method' => $action,
                        'controller' => $controller,
                        'params' => $params,
                        'seo_url' => $seo_url
                    ];
                }
            }

        } catch (\ReflectionException $e) {
            return [];
        }

        return $actions;
    }

}