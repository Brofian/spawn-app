<?php


namespace spawnApp\Controller\Backend;

use spawn\system\Core\Base\Controller\AbstractBackendController;
use spawn\system\Core\Services\ServiceTags;
use spawnApp\Models\RewriteUrl;

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

        $this->twig->assign('available_controllers', $this->getAvailableControllerActions());

        //load already saved dbHelper
        $dbHelper = $this->container->get('system.database.helper');
        $rewriteUrls = RewriteUrl::loadAll($dbHelper);
        $this->twig->assign('rewrite_urls', $rewriteUrls);



        $this->twig->assign('content_file', 'backend/contents/seo_url_config/content.html.twig');
    }


    protected function getAvailableControllerActions(): array {
        //load available controller action combinations
        $controllerServices = $this->container->getServicesByTag(ServiceTags::BASE_CONTROLLER);
        $actions = [];
        foreach($controllerServices as $controllerService) {
            try {
                $class = new \ReflectionClass($controllerService->getClass());
                $methods = $class->getMethods(\ReflectionMethod::IS_PUBLIC);

                /** @var \ReflectionMethod $method */
                foreach($methods as $method) {
                    if(strpos($method->getName(), '__') !== 0 && preg_match('/^.*Action$/m', $method->getName())) {
                        $actions[] = [
                            'method' => $method->getName(),
                            'controller' => $method->getDeclaringClass()->getName()
                        ];
                    }
                }

            } catch (\ReflectionException $e) {}
        }

        return $actions;
    }

}