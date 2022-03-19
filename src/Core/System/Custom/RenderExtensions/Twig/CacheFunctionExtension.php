<?php declare(strict_types = 1);
namespace SpawnCore\System\Custom\RenderExtensions\Twig;


use SpawnCore\System\CardinalSystem\ModuleNetwork\ModuleNamespacer;
use SpawnCore\System\Custom\RenderExtensions\Twig\Abstracts\FunctionExtension;

class CacheFunctionExtension extends FunctionExtension
{

    /**
     * @return string
     */
    protected function getFunctionName(): string
    {
        return "cache";
    }


    /**
     * @return callable
     */
    protected function getFunctionFunction(): callable
    {
        return static function ($namespace) {

            $namespace = ModuleNamespacer::hashNamespace($namespace);

            return '/cache/' . $namespace . '/';
        };
    }

    /**
     * @return array
     */
    protected function getFunctionOptions(): array
    {
        return [];
    }
}