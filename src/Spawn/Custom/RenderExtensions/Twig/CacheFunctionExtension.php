<?php

namespace spawnCore\Custom\RenderExtensions\Twig;


use spawnCore\CardinalSystem\ModuleNetwork\ModuleNamespacer;
use spawnCore\Custom\RenderExtensions\Twig\Abstracts\FunctionExtension;

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
        return function ($namespace) {

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