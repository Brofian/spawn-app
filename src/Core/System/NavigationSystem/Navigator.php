<?php

namespace spawnCore\System\NavigationSystem;

use SpawnCore\Defaults\Database\SeoUrlTable\SeoUrlEntity;
use SpawnCore\Defaults\Services\ConfigurationManager;
use SpawnCore\System\Custom\Gadgets\CUriConverter;
use SpawnCore\System\Custom\Gadgets\UUID;
use SpawnCore\System\Custom\Throwables\DatabaseConnectionException;
use SpawnCore\System\Database\Criteria\Criteria;
use SpawnCore\System\Database\Criteria\Filters\EqualsFilter;
use SpawnCore\System\Database\Entity\RepositoryException;
use SpawnCore\System\Database\Entity\TableRepository;
use SpawnCore\System\ServiceSystem\ServiceContainer;
use SpawnCore\System\ServiceSystem\ServiceContainerProvider;

class Navigator
{
    public const FALLBACK_CONFIG = 'config_system_fallback_method';

    protected SeoUrlEntity $fallbackEntity;
    protected ServiceContainer $serviceContainer;
    protected TableRepository $seoUrlRepository;


    /**
     * @throws DatabaseConnectionException
     * @throws RepositoryException
     */
    public function __construct()
    {
        $this->serviceContainer = ServiceContainerProvider::getServiceContainer();
        $this->seoUrlRepository = $this->serviceContainer->getServiceInstance('seo_urls.repository');

        
        //load fallback service and action
        /** @var ConfigurationManager $configurationManager */
        $configurationManager = $this->serviceContainer->getServiceInstance('system.service.configuration_manager');
        $fallbackActionID = $configurationManager->getConfiguration(self::FALLBACK_CONFIG);
        $fallbackEntity = false;
        if($fallbackActionID) {
            $fallbackEntity = $this->seoUrlRepository->search(new Criteria(new EqualsFilter('id', UUID::hexToBytes($fallbackActionID))))->first();
        }

        if(!$fallbackEntity) {
            $fallbackEntity = $this->seoUrlRepository->search(new Criteria(new EqualsFilter('name', 'app.fallback.404')))->first();
        }

        $this->fallbackEntity = $fallbackEntity;
    }


    /**
     * Get a seo url entity by the provided route
     */
    public function route(string $name): SeoUrlEntity
    {
        if (!$name) {
            return $this->fallbackEntity;
        }

        $seoUrlEntity = $this->getSeoUrlByName($name);

        return $seoUrlEntity ?? $this->fallbackEntity;
    }


    public function getSeoEntityById(string $id): ?SeoUrlEntity {
        return $this->seoUrlRepository->search(new Criteria(
            new EqualsFilter('id', UUID::hexToBytes($id))
        ))->first();
    }


    /**
     * @throws DatabaseConnectionException
     * @throws RepositoryException
     */
    public function rewriteURL(string $original, array &$values): SeoUrlEntity
    {
        $original = '/'.trim($original, '/? #');
        //$original = "/[whatever]"


        $rewrite_urls = $this->seoUrlRepository->search(
            new Criteria(new EqualsFilter('active', true))
        );

        /** @var SeoUrlEntity $seo_url */
        foreach ($rewrite_urls as $seo_url) {
            $regex = CUriConverter::cUriToRegex($seo_url->getCUrl());

            $matches = [];
            $hasMatched = preg_match($regex, $original, $matches);

            if ($hasMatched) {
                array_shift($matches);
                $values = array_values($matches);
                return $seo_url;
            }
        }

        return $this->fallbackEntity;
    }


    public function getSeoLinkByParameters(string $name, array $parameters = []): string
    {
        if (!$name) {
            return $this->getSeoUrlFromEntity($this->fallbackEntity, $parameters);
        }

        $seoUrl = $this->getSeoUrlByName($name);

        return $this->getSeoUrlFromEntity($seoUrl ?? $this->fallbackEntity, $parameters);
    }


    public function getSeoUrlFromEntity(SeoUrlEntity $seoUrlEntity, array $parameters): string {
        return CUriConverter::cUriToUri($seoUrlEntity->getCUrl(), $parameters);
    }


    protected function getSeoUrlByName(string $name): ?SeoUrlEntity {
        return $this->seoUrlRepository->search(
            new Criteria(
                new EqualsFilter('name', $name)
            )
        )->first();
    }

    public function getFallbackEntity(): SeoUrlEntity {
        return $this->fallbackEntity;
    }

}