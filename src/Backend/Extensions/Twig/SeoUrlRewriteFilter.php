<?php declare(strict_types = 1);
namespace SpawnBackend\Extensions\Twig;


use SpawnCore\System\Custom\RenderExtensions\Twig\Abstracts\FunctionExtension;
use SpawnCore\System\NavigationSystem\Navigator;
use SpawnCore\System\ServiceSystem\ServiceContainerProvider;

class SeoUrlRewriteFilter extends FunctionExtension {


    protected function getFunctionName(): string
    {
        return "seo_url";
    }



    protected function getFunctionFunction(): callable
    {
        return static function ($controller = null, $action = null, array $parameters = []) {

            if(!preg_match('/^.*Action$/m', $action)) {
                $action .= 'Action';
            }

            /** @var Navigator $routingHelper */
            $routingHelper = ServiceContainerProvider::getServiceContainer()->getServiceInstance('system.routing.helper');


            return $routingHelper->getSeoLinkByParameters($controller, $action, $parameters);
        };
    }


    protected function getFunctionOptions(): array
    {
        return [
            'needs_context' => false,
        ];
    }
}