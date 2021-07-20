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

        //load available controller action combinations
        $controllerServices = $this->container->getServicesByTag(ServiceTags::BASE_CONTROLLER);
        $actions = [];
        foreach($controllerServices as $controllerService) {
            $actions = $this->getActionsFromController($controllerService->getClass());
        }

        $this->twig->assign('available_controllers', $actions);

        //load already saved dbHelper
        $dbHelper = $this->container->get('system.database.helper');
        $rewriteUrls = RewriteUrl::loadAll($dbHelper);
        $this->twig->assign('rewrite_urls', $rewriteUrls);



        $this->twig->assign('content_file', 'backend/contents/seo_url_config/content.html.twig');
    }


    protected function getActionsFromController(string $controllerCls) {

        $actions = get_class_methods($controllerCls);

        //entferne __construct
        //nur public methods
        //füge controller zu array einträgen hinzu

        return $actions;
    }

}