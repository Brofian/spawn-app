<?php

namespace SpawnCore\System\Custom\RenderExtensions\Twig;


use SpawnCore\System\Custom\RenderExtensions\Twig\Abstracts\FunctionExtension;

class DumpFunctionExtension extends FunctionExtension
{

    /**
     * @return string
     */
    protected function getFunctionName(): string
    {
        return "dump";
    }


    /**
     * @return callable
     */
    protected function getFunctionFunction(): callable
    {
        return static function ($context, $var = "nothingtoseehere") {
            if ($var === "nothingtoseehere") {
                dump($context);
            } else {
                dump($var);
            }
        };
    }

    /**
     * @return array
     */
    protected function getFunctionOptions(): array
    {
        return [
            'needs_context' => true,
        ];
    }
}