<?php

namespace SpawnCore\System\Custom\RenderExtensions\Twig\Abstracts;


use Twig\Environment;
use Twig\TwigFunction;

abstract class FunctionExtension
{

    /**
     * @param Environment $twig
     */
    public function addToTwig(Environment &$twig)
    {
        $function = new TwigFunction(
            $this->getFunctionName(),
            $this->getFunctionFunction(),
            $this->getFunctionOptions()
        );

        $twig->addFunction($function);
    }

    /**
     * @return string
     */
    abstract protected function getFunctionName(): string;

    /**
     * @return callable
     */
    abstract protected function getFunctionFunction(): callable;

    /**
     * @return array
     */
    abstract protected function getFunctionOptions(): array;


}