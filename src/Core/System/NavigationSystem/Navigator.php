<?php

namespace spawnCore\System\NavigationSystem;

use SpawnCore\Defaults\Database\SeoUrlTable\SeoUrlEntity;
use SpawnCore\Defaults\Database\SeoUrlTable\SeoUrlRepository;
use SpawnCore\Defaults\Services\ConfigurationManager;
use SpawnCore\System\Cron\CronStates;
use SpawnCore\System\Custom\Gadgets\CUriConverter;
use SpawnCore\System\Custom\Gadgets\UUID;
use SpawnCore\System\Database\Criteria\Criteria;
use SpawnCore\System\Database\Criteria\Filters\AndFilter;
use SpawnCore\System\Database\Criteria\Filters\EqualsFilter;
use SpawnCore\System\ServiceSystem\Service;
use SpawnCore\System\ServiceSystem\ServiceContainer;
use SpawnCore\System\ServiceSystem\ServiceContainerProvider;

class Navigator
{
    public const FALLBACK_CONFIG = 'config_system_fallback_method';
    protected string $fallbackService = 'system.fallback.404';
    protected string $fallbackAction = 'error404Action';

    protected ServiceContainer $serviceContainer;
    protected SeoUrlRepository $seoUrlRepository;


    public function __construct()
    {
        $this->serviceContainer = ServiceContainerProvider::getServiceContainer();
        $this->seoUrlRepository = $this->serviceContainer->getServiceInstance('system.repository.seo_urls');

        
        //load fallback service and action
        /** @var ConfigurationManager $configurationManager */
        $configurationManager = $this->serviceContainer->getServiceInstance('system.service.configuration_manager');
        $fallbackActionID = $configurationManager->getConfiguration(self::FALLBACK_CONFIG);
        if($fallbackActionID) {
            $seoUrlEntity = $this->seoUrlRepository->search(new Criteria(new EqualsFilter('id', UUID::hexToBytes($fallbackActionID))))->first();
            if($seoUrlEntity instanceof SeoUrlEntity) {
                $this->fallbackAction = $seoUrlEntity->getAction();
                $this->fallbackService = $seoUrlEntity->getController();
            }
        }
    }


    public function route(string $controller, string $action, ?Service &$controllerCls, ?string &$actionStr): void
    {
        if ($controller == "" || $action == "") {
            $controllerCls = $this->serviceContainer->getService($this->fallbackService);
            $actionStr = $this->fallbackAction;
            return;
        }

        //find service
        $controllerCls = $this->serviceContainer->getService($controller);
        if (!$controllerCls) {
            //controller does not exist
            $controllerCls = $this->serviceContainer->getService($this->fallbackService);
            $actionStr = $this->fallbackAction;
            return;
        }

        if (!preg_match('/^.*Action$/m', $action)) {
            $actionStr = $action . "Action";
        } else {
            $actionStr = $action;
        }

        if (!method_exists($controllerCls->getClass(), $actionStr)) {
            //action does not exist
            $controllerCls = $this->serviceContainer->getService($this->fallbackService);
            $actionStr = $this->fallbackAction;
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


        $rewrite_urls = $this->seoUrlRepository->search(
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

        return self::getFormattedLink($this->fallbackService, $this->fallbackAction);
    }

    public static function getFormattedLink(string $controller, string $action): string
    {
        return "/?controller=$controller&action=$action";
    }

    public function getSeoLinkByParameters(?string $controller, ?string $action, array $parameters = []): string
    {

        if ($controller == null || $action == null) {
            return self::getSeoLinkByParameters($this->fallbackService, $this->fallbackAction);
        }

        $seoUrlCollection = $this->seoUrlRepository->search(
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
            return self::getSeoLinkByParameters($this->fallbackService, $this->fallbackAction);
        }
    }


}