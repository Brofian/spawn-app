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
        return static function (string $name, array $parameters = []) {

            /** @var Navigator $routingHelper */
            $routingHelper = ServiceContainerProvider::getServiceContainer()->getServiceInstance('system.routing.helper');

            return $routingHelper->getSeoLinkByParameters($name, $parameters);
        };
    }


    protected function getFunctionOptions(): array
    {
        return [
            'needs_context' => false,
        ];
    }
}