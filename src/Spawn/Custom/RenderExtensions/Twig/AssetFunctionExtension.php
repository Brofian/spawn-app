<?php

namespace spawnCore\Custom\RenderExtensions\Twig;


use spawnCore\CardinalSystem\ModuleNetwork\ModuleNamespacer;
use spawnCore\Custom\RenderExtensions\Twig\Abstracts\FunctionExtension;

class AssetFunctionExtension extends FunctionExtension
{

    /**
     * @return string
     */
    protected function getFunctionName(): string
    {
        return "assetPath";
    }


    /**
     * @return callable
     */
    protected function getFunctionFunction(): callable
    {
        return function ($namespace, $doHash = false) {

            if ($doHash) {
                $namespace = ModuleNamespacer::hashNamespace($namespace);
            }

            return 'http://' . MAIN_ADDRESS . '/pack/' . $namespace . '/';
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