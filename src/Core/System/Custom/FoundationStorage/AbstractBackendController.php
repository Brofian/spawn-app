<?php declare(strict_types=1);

namespace SpawnCore\System\Custom\FoundationStorage;

abstract class AbstractBackendController extends AbstractController
{

    public function __construct()
    {
        parent::__construct();

        //assign backend sidebar tree
        $sidebarLinks = $this->gatherSidebarLinks();
        $this->twig->assign('sidebar_tree', $sidebarLinks);
        //assign fallback backend content template
        $this->twig->assign('content_file', 'backend/home/content.html.twig');
        //set base twig entry point for backend
        $this->twig->setRenderFile('backend/index.html.twig');
    }

    public final function gatherSidebarLinks(): array
    {
        $backendControllerServices = $this->container->getServicesByTag('backend.controller');

        $sidebarStructure = [];

        foreach ($backendControllerServices as $serviceKey => $backendControllerService) {
            /** @var AbstractBackendController $class */
            $class = $backendControllerService->getClass();
            $classSidebarMethods = $class::getSidebarMethods();

            //replace %self.key% with the service key
            replaceStringInArrayRecursive('%self.key%', $serviceKey, $classSidebarMethods);

            $sidebarStructure = array_merge_recursive(
                $sidebarStructure,
                $classSidebarMethods,
            );
        }


        $this->filterDuplicateArrayElements($sidebarStructure);

        return $sidebarStructure;
    }

    private function filterDuplicateArrayElements(array &$array, bool $isFirst = true): void {

        foreach($array as $key => &$element) {
            if($isFirst) {
                $this->filterDuplicateArrayElements($element, false);
            }
            elseif(is_array($element)) {
                switch($key) {
                    case 'title':
                    case 'color':
                    case 'controller':
                        $element = $element[0];
                        break;
                    case 'actions':
                        $this->filterDuplicateArrayElements($element, false);
                        break;
                }
            }
        }
    }


    abstract public static function getSidebarMethods(): array;


}