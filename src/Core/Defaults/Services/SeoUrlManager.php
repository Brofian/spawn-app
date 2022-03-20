<?php

namespace SpawnCore\Defaults\Services;

use Doctrine\DBAL\Exception;
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

    /**
     * @throws DatabaseConnectionException
     * @throws Exception
     * @throws RepositoryException
     */
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

    /**
     * @param string $controller
     * @param string $method
     * @return SeoUrlEntity|null
     * @throws DatabaseConnectionException
     * @throws RepositoryException
     */
    public function getSeoUrl(string $controller, string $method): ?SeoUrlEntity
    {
        return $this->seoUrlRepository->search(
            new Criteria(new AndFilter(
                new EqualsFilter('controller', $controller),
                new EqualsFilter('action', $method)
            ))
        )->first();
    }

    /**
     * @param SeoUrlEntity $seoUrlEntity
     * @throws Exception
     * @throws WrongEntityForRepositoryException
     * @throws DatabaseConnectionException
     */
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