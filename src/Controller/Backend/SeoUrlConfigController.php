<?php


namespace spawnApp\Controller\Backend;

use spawn\system\Core\Base\Controller\AbstractBackendController;
use spawn\system\Core\Base\Database\Definition\EntityCollection;
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

        $this->twig->assign('content_file', 'backend/contents/seo_url_config/content.html.twig');
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
            try {
                $class = new \ReflectionClass($controllerService->getClass());
                $methods = $class->getMethods(\ReflectionMethod::IS_PUBLIC);

                /** @var \ReflectionMethod $method */
                foreach($methods as $method) {
                    if(strpos($method->getName(), '__') !== 0 && preg_match('/^.*Action$/m', $method->getName())) {

                        $action = $method->getName();
                        $controller = $controllerService->getId();
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
                            'seo_url' => $seo_url
                        ];
                    }
                }

            } catch (\ReflectionException $e) {}
        }

        return $actions;
    }

}