<?php

namespace SpawnCore\Defaults\Services;

use bin\spawn\IO;
use Doctrine\DBAL\Exception;
use http\Exception\RuntimeException;
use SpawnCore\Defaults\Database\SeoUrlTable\SeoUrlEntity;
use SpawnCore\Defaults\Database\SeoUrlTable\SeoUrlRepository;
use SpawnCore\Defaults\Database\SeoUrlTable\SeoUrlTable;
use SpawnCore\System\Custom\Gadgets\ClassInspector;
use SpawnCore\System\Custom\Gadgets\MethodInspector;
use SpawnCore\System\Custom\Gadgets\UUID;
use SpawnCore\System\Custom\Throwables\DatabaseConnectionException;
use SpawnCore\System\Custom\Throwables\WrongEntityForRepositoryException;
use SpawnCore\System\Database\Criteria\Criteria;
use SpawnCore\System\Database\Criteria\Filters\AndFilter;
use SpawnCore\System\Database\Criteria\Filters\EqualsFilter;
use SpawnCore\System\Database\Entity\EntityCollection;
use SpawnCore\System\Database\Entity\InvalidRepositoryInteractionException;
use SpawnCore\System\Database\Entity\RepositoryException;
use SpawnCore\System\Database\Helpers\DatabaseConnection;
use SpawnCore\System\ServiceSystem\Service;
use SpawnCore\System\ServiceSystem\ServiceContainer;
use SpawnCore\System\ServiceSystem\ServiceContainerProvider;
use SpawnCore\System\ServiceSystem\ServiceTags;

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

    /**
     * @throws DatabaseConnectionException
     * @throws RepositoryException
     */
    public function getSeoUrls(bool $ignoreLocked = false, int $limit = 9999, int $offset = 0): EntityCollection {
        $criteria = new Criteria();
        if($ignoreLocked) {
            $criteria->addFilter(new EqualsFilter('locked', 0));
        }

        return $this->seoUrlRepository->search($criteria, $limit, $offset);
    }

    /**
     * @throws DatabaseConnectionException
     * @throws Exception
     */
    public function getNumberAvailableSeoUrls(bool $ignoreLocked = false): int {
        $queryBuilder = DatabaseConnection::getConnection()->createQueryBuilder();

        $stmt = $queryBuilder
            ->select('COUNT(*) as count')
            ->from(SeoUrlTable::TABLE_NAME);
        if($ignoreLocked) {
           $stmt->where('locked = 0');
        }

        return $stmt->executeQuery()->fetchAssociative()['count'];
    }

    public function getSeoUrl(string $controller, string $method): ?SeoUrlEntity
    {
        return $this->seoUrlRepository->search(
            new Criteria(new AndFilter(
                new EqualsFilter('controller', $controller),
                new EqualsFilter('action', $method)
            ))
        )->first();
    }


    public function getSeoUrlFromId(string $id): ?SeoUrlEntity {
        return $this->seoUrlRepository->search(
            new Criteria(new EqualsFilter('id', UUID::hexToBytes($id)))
        )->first();
    }

    public function getSeoUrlFromName(string $name): ?SeoUrlEntity {
        return $this->seoUrlRepository->search(
            new Criteria(new EqualsFilter('name', $name))
        )->first();
    }

    public function saveSeoUrlEntity(SeoUrlEntity $seoUrlEntity): void {
        $this->seoUrlRepository->upsert($seoUrlEntity);
    }


    /**
     *  This part is used for "bin/console modules:refresh-actions"
     * @param bool $removeStaleEntries
     * @return array
     * @throws DatabaseConnectionException
     * @throws Exception
     * @throws RepositoryException
     * @throws WrongEntityForRepositoryException
     * @throws InvalidRepositoryInteractionException
     */
    public function refreshSeoUrlEntries(bool $removeStaleEntries = true): array
    {
        /** @var SeoUrlEntity[] $registeredSeoUrls */
        $registeredSeoUrls = [];
        /** @var SeoUrlEntity $seoUrl */
        foreach($this->getSeoUrls(false) as $seoUrl) {
            $registeredSeoUrls[$seoUrl->getName()] = $seoUrl;
        }

        /** @var ClassInspector[string] $availableControllers */
        $availableControllers = $this->getEveryController();


        $result = [
            'added' => 0,
            'updated' => 0
        ];
        // Add controller actions, that have no entry in the database yet
        /**
         * @var string $controllerServiceId
         * @var ClassInspector $inspectedController
         */
        foreach($availableControllers as $controllerServiceId => $inspectedController) {
            foreach($inspectedController->getLoadedMethods() as $inspectedMethod) {
                $cUrl = $inspectedMethod->getTag('route', '');
                $isLocked = $inspectedMethod->getTag('locked', false);
                $name = $inspectedMethod->getTag('name', null);
                $isApi = $inspectedMethod->getTag('api', false);

                $requiresTags = $inspectedMethod->getTag('requires', '');
                $requiresUser = false;
                $requiresAdmin = false;
                if(is_array($requiresTags) && count($requiresTags)) {
                    $firstOccuringRequiresTag = $requiresTags[0];
                    if(count($firstOccuringRequiresTag)) {
                        $firstParameter = $firstOccuringRequiresTag[0];

                        $requiresTags = explode(',',$firstParameter);
                        $requiresAdmin = in_array('admin', $requiresTags, true);
                        $requiresUser = in_array('login', $requiresTags, true);
                    }
                }







                if(!$name) {
                    IO::printWarning('# Missing tag "@name" in action definition in ' . $controllerServiceId);
                    continue;
                }
                if(!$cUrl) {
                    IO::printWarning('# Missing tag "@route" in action definition in ' . $controllerServiceId);
                    continue;
                }

                if(!isset($registeredSeoUrls[$name])) {

                    $seoUrl = new SeoUrlEntity(
                        $name,
                        $cUrl,
                        $controllerServiceId,
                        $inspectedMethod->getMethodName(),
                        $inspectedMethod->getParameters(),
                        $isLocked,
                        false,
                        $requiresAdmin,
                        $requiresUser,
                        $isApi
                    );

                    $this->seoUrlRepository->upsert($seoUrl);
                    $registeredSeoUrls[$name] = $seoUrl;
                    $result['added']++;
                }
                else {
                    $seoUrl = $registeredSeoUrls[$name];
                    $oldSeoUrl = clone $seoUrl;

                    $seoUrl->setLocked($isLocked);
                    $seoUrl->setController($controllerServiceId);
                    $seoUrl->setAction($inspectedMethod->getMethodName());
                    $seoUrl->setParameters($inspectedMethod->getParameters());
                    $seoUrl->setApi($isApi);
                    $seoUrl->setRequiresUser($requiresUser);
                    $seoUrl->setRequiresAdmin($requiresAdmin);
                    if($isLocked) {
                        $seoUrl->setCUrl($cUrl);
                    }

                    if(!$seoUrl->compareSeoUrlEntity($oldSeoUrl)) {
                        $this->seoUrlRepository->upsert($seoUrl);
                        $result['updated']++;
                    }
                }
            }
        }



        if($removeStaleEntries) {
            //remove the old actions from $registeredSeoUrls
            $result['removed'] = 0;

            /** @var SeoUrlEntity $registeredSeoUrl */
            foreach($registeredSeoUrls as $registeredSeoUrl) {
                $isInUse = false;

                /**
                 * @var string $controllerServiceId
                 * @var ClassInspector $inspectedController
                 */
                foreach($availableControllers as $controllerServiceId => $inspectedController) {
                    foreach($inspectedController->getLoadedMethods() as $inspectedMethod) {
                        $name = $inspectedMethod->getTag('name', '');
                        if($name && $name === $registeredSeoUrl->getName()) {
                            $isInUse = true;
                            break 2;
                        }
                    }
                }

                if(!$isInUse) {
                    $this->seoUrlRepository->delete(
                        new Criteria(new EqualsFilter('id', UUID::hexToBytes($registeredSeoUrl->getId())))
                    );
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