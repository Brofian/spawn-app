<?php

namespace spawnApp\Extensions\Twig;

use spawn\system\Core\Extensions\Twig\Abstracts\FunctionExtension;
use spawn\system\Core\Helper\RoutingHelper;
use spawn\system\Core\Services\ServiceContainerProvider;

class SeoUrlRewriteFilter extends FunctionExtension {


    protected function getFunctionName(): string
    {
        return "seo_url";
    }



    protected function getFunctionFunction(): callable
    {
        return function ($controller = null, $action = null, array $parameters = []) {

            if(!preg_match('/^.*Action$/m', $action)) {
                $action .= 'Action';
            }

            /** @var RoutingHelper $routingHelper */
            $routingHelper = ServiceContainerProvider::getServiceContainer()->getServiceInstance('system.routing.helper');


            $seoLink = $routingHelper->getSeoLinkByParameters($controller, $action, $parameters);

            return $seoLink;
        };
    }


    protected function getFunctionOptions(): array
    {
        return [
            'needs_context' => false,
        ];
    }
}