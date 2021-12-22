<?php

namespace spawnCore\NavigationSystem;

use spawnApp\Database\SeoUrlTable\SeoUrlEntity;
use spawnApp\Database\SeoUrlTable\SeoUrlRepository;
use spawnCore\Cron\CronStates;
use spawnCore\Custom\Gadgets\CUriConverter;
use spawnCore\Database\Criteria\Criteria;
use spawnCore\Database\Criteria\Filters\AndFilter;
use spawnCore\Database\Criteria\Filters\EqualsFilter;
use spawnCore\ServiceSystem\Service;
use spawnCore\ServiceSystem\ServiceContainer;
use spawnCore\ServiceSystem\ServiceContainerProvider;

class Navigator
{
    const FALLBACK_SERVICE = 'system.fallback.404';
    const FALLBACK_ACTION = 'error404Action';


    protected ServiceContainer $serviceContainer;

    public function __construct()
    {
        $this->serviceContainer = ServiceContainerProvider::getServiceContainer();
    }


    public function route(string $controller, string $action, ?Service &$controllerCls, ?string &$actionStr): void
    {

        if ($controller == "" || $action == "") {
            $controllerCls = $this->serviceContainer->getService(self::FALLBACK_SERVICE);
            $actionStr = self::FALLBACK_ACTION;
            return;
        }

        //find service
        $controllerCls = $this->serviceContainer->getService($controller);
        if (!$controllerCls) {
            //controller does not exist
            $controllerCls = $this->serviceContainer->getService(self::FALLBACK_SERVICE);
            $actionStr = self::FALLBACK_ACTION;
            return;
        }

        if (!preg_match('/^.*Action$/m', $action)) {
            $actionStr = $action . "Action";
        } else {
            $actionStr = $action;
        }

        if (!method_exists($controllerCls->getClass(), $actionStr)) {
            //action does not exist
            $controllerCls = $this->serviceContainer->getService(self::FALLBACK_SERVICE);
            $actionStr = self::FALLBACK_ACTION;
            return;
        }

        return;
    }


    public function rewriteURL(string $original, array &$values): string
    {

        $original = trim($original, '/? #');
        if ($original == '' || strlen($original)) {
            $original = '/' . $original;
        }
        //$original = "/[whatever]"

        /** @var SeoUrlRepository $seoUrlRepository */
        $seoUrlRepository = $this->serviceContainer->getServiceInstance('system.repository.seo_urls');
        $rewrite_urls = $seoUrlRepository->search(
            new Criteria(new EqualsFilter('active', true))
        );

        /** @var SeoUrlEntity $seo_url */
        foreach ($rewrite_urls as $seo_url) {
            $regex = CUriConverter::cUriToRegex($seo_url->getCUrl());

            $matches = [];
            $hasMatched = preg_match($regex, $original, $matches);

            if ($hasMatched) {

                for ($i = 1; $i < count($matches); $i++) {
                    $values[] = $matches[$i];
                }

                return self::getFormattedLink($seo_url->getController(), $seo_url->getAction());
            }
        }

        return self::getFormattedLink('system.fallback.404', 'error404');
    }

    public static function getFormattedLink(string $controller, string $action): string
    {
        return "/?controller=$controller&action=$action";
    }

    public function getSeoLinkByParameters(?string $controller, ?string $action, array $parameters = []): string
    {

        if ($controller == null || $action == null) {
            return self::getSeoLinkByParameters(self::FALLBACK_SERVICE, self::FALLBACK_ACTION);
        }

        /** @var SeoUrlRepository $seoUrlRepository */
        $seoUrlRepository = $this->serviceContainer->getServiceInstance('system.repository.seo_urls');
        $seoUrlCollection = $seoUrlRepository->search(
            new Criteria(
                new AndFilter(
                    new EqualsFilter('controller', $controller),
                    new EqualsFilter('action', $action)
                )
            )
        );


        $seoUrl = $seoUrlCollection->first();


        if ($seoUrl instanceof SeoUrlEntity) {
            $cUrl = $seoUrl->getCUrl();
            return CUriConverter::cUriToUri($cUrl, $parameters);
        } else {
            return self::getSeoLinkByParameters(self::FALLBACK_SERVICE, self::FALLBACK_ACTION);
        }
    }


}