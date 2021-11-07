<?php

namespace spawnApp\Services;

use Doctrine\DBAL\Exception;
use spawn\system\Core\Base\Database\Definition\EntityCollection;
use spawn\system\Core\Custom\ClassInspector;
use spawn\system\Core\Custom\MethodInspector;
use spawn\system\Core\Helper\UUID;
use spawn\system\Core\Services\Service;
use spawn\system\Core\Services\ServiceContainer;
use spawn\system\Core\Services\ServiceContainerProvider;
use spawn\system\Core\Services\ServiceTags;
use spawn\system\Throwables\WrongEntityForRepositoryException;
use spawnApp\Database\SeoUrlTable\SeoUrlEntity;
use spawnApp\Database\SeoUrlTable\SeoUrlRepository;

class SeoUrlManager {

    protected SeoUrlRepository $seoUrlRepository;
    protected ServiceContainer $serviceContainer;

    public function __construct(
        SeoUrlRepository $seoUrlRepository
    )
    {
        $this->seoUrlRepository = $seoUrlRepository;
        $this->serviceContainer = ServiceContainerProvider::getServiceContainer();
    }

    public function getSeoUrls(int $limit = 0, int $offset = 0): EntityCollection {
        if($limit !== 0) {
            return $this->seoUrlRepository->search([], $limit, $offset);
        }
        return $this->seoUrlRepository->search();
    }

    /**
     * @param string $controller
     * @param string $method
     * @return SeoUrlEntity|null
     */
    public function getSeoUrl(string $controller, string $method) {
        return $this->seoUrlRepository->search([
            'controller' => $controller,
            'action' => $method
        ])->first();
    }

    public function saveSeoUrlEntity(SeoUrlEntity $seoUrlEntity): void {
        $this->seoUrlRepository->upsert($seoUrlEntity);
    }


    /**
     *  This part is used for "bin/console modules:refresh-actions"
     * @param bool $removeStaleEntries
     * @return array
     * @throws Exception
     * @throws WrongEntityForRepositoryException
     */
    public function refreshSeoUrlEntries(bool $removeStaleEntries = true) {
        /** @var EntityCollection $registeredSeoUrls */
        $registeredSeoUrls = $this->getSeoUrls();
        /** @var ClassInspector[string] $availableControllers */
        $availableControllers = $this->getEveryController();

        $result = [
            'added' => 0
        ];
        // Add controller actions, that have no entry in the database yet
        foreach($availableControllers as $controllerServiceId => $inspectedController) {
            foreach($inspectedController->getLoadedMethods() as $inspectedMethod) {
                $isNew = true;

                /** @var SeoUrlEntity $registeredSeoUrl */
                foreach($registeredSeoUrls->getArray() as $registeredSeoUrl) {
                    if( $registeredSeoUrl->getController() == $controllerServiceId &&
                        $registeredSeoUrl->getAction() == $inspectedMethod->getMethodName())
                    {
                        $isNew = false;
                        break;
                    }
                }

                if($isNew) {

                    $this->seoUrlRepository->upsert(
                        new SeoUrlEntity(
                            $inspectedMethod->getTag('route', ''),
                            $controllerServiceId,
                            $inspectedMethod->getMethodName(),
                            $inspectedMethod->getParameters(),
                            $inspectedMethod->getTag('locked', false),
                            true,
                        )
                    );
                    $result['added']++;
                }

            }
        }



        if($removeStaleEntries) {
            //remove the old actions from $registeredSeoUrls
            $result['removed'] = 0;

            /** @var SeoUrlEntity $registeredSeoUrl */
            foreach($registeredSeoUrls->getArray() as $registeredSeoUrl) {
                $isInUse = false;

                /**
                 * @var string $controllerServiceId
                 * @var ClassInspector $inspectedController
                 */
                foreach($availableControllers as $controllerServiceId => $inspectedController) {
                    foreach($inspectedController->getLoadedMethods() as $inspectedMethod) {
                        if( $registeredSeoUrl->getController() == $controllerServiceId &&
                            $registeredSeoUrl->getAction() == $inspectedMethod->getMethodName())
                        {
                            $isInUse = true;
                            break;
                        }
                    }
                }

                if(!$isInUse) {
                    $this->seoUrlRepository->delete([
                        'id' => UUID::hexToBytes($registeredSeoUrl->getId())
                    ]);
                    $result['removed']++;
                }
            }

        }

        return $result;
    }

    protected function getEveryController(): array {
        /** @var Service[] $controllerServices */
        $controllerServices = $this->getEveryControllerService();
        $list = [];

        foreach($controllerServices as $serviceId => $controllerService) {
            $controller = new ClassInspector($controllerService->getClass(), function(MethodInspector $element) {
                $isMagicMethod = str_starts_with($element->getMethodName(), '__');
                $isControllerActionMethod = str_ends_with($element->getMethodName(), 'Action');
                $isPublic = $element->isPublic();
                return (!$isMagicMethod && $isControllerActionMethod && $isPublic);
            });
            $controller->set('serviceId', $serviceId);

            $list[$serviceId] = $controller;
        }

        return $list;
    }

    protected function getEveryControllerService(): array {
        return $this->serviceContainer->getServicesByTags(
            [
                ServiceTags::BASE_CONTROLLER,
                ServiceTags::BACKEND_CONTROLLER,
            ]
        );
    }


}