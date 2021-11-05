<?php

namespace spawnApp\Services;

use Doctrine\DBAL\Exception;
use spawn\system\Core\Base\Database\Definition\EntityCollection;
use spawn\system\Core\Services\Service;
use spawn\system\Core\Services\ServiceContainer;
use spawn\system\Core\Services\ServiceContainerProvider;
use spawn\system\Core\Services\ServiceTags;
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
     */
    public function refreshSeoUrlEntries(bool $removeStaleEntries = true) {
        $registeredSeoUrls = $this->getSeoUrls();
        $availableControllerActions = $this->getEveryControllerAction();
        dd($availableControllerActions);
        //TODO add new actions from $availableControllerActions

        if($removeStaleEntries) {
            //TODO remove the old actions from $registeredSeoUrls
        }

    }

    protected function getEveryControllerAction(): array {
        /** @var Service[] $controllerServices */
        $controllerServices = $this->getEveryControllerService();

        $list = [];


        foreach($controllerServices as $controllerService) {
            $methods = $this->getPublicMethodsFromClass($controllerService->getClass());
            $list[$controllerService->getId()] = $methods;
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


    protected function getPublicMethodsFromClass(string $class): array {
        try {
            $class = new \ReflectionClass($class);
            $methods = $class->getMethods(\ReflectionMethod::IS_PUBLIC);
            $list = [];

            /** @var \ReflectionMethod $method */
            foreach ($methods as $method) {
                //  Prevent magic methods like __construct from beeing detected
                //  and also allow only methods, that end with the suffix "Action"
                if (strpos($method->getName(), '__') !== 0 && preg_match('/^.*Action$/m', $method->getName())) {
                    $list[$method->getName()] = [
                        'parameters' => $method->getParameters(),
                        'doc' => $this->getFormattedPhpDoc($method->getDocComment())
                    ];
                }
            }

            return $list;
        }
        catch (\Exception $e) {
            return [];
        }
    }

    protected function getFormattedPhpDoc(string $phpDoc): array {
        if($phpDoc == '') {
            return [];
        }

        $phpDocArray = [];

        //splice every valid line into three parameters, the first beginning with an @
        preg_match_all('/@([^ ]*) ([^\n]*)/m', $phpDoc, $phpDocData, PREG_SET_ORDER);
        dd($phpDocData);

        foreach($phpDocData as $data) {
            $type = $data[1];
            $value = $data[2];

            switch ($type) {
                case 'param':
                    $values = explode(' ', $value);
                    if(count($values) == 1)     $phpDocArray['param'][$values[0]] = 'mixed';
                    elseif(count($values) > 1)  $phpDocArray['param'][$values[1]] = $values[0];
                    break;
                case 'return':
                    $phpDocArray['return'] = $value;
                    break;
                default:
                    // TODO
                    break;
            }
        }


        return $phpDocArray;
    }


}