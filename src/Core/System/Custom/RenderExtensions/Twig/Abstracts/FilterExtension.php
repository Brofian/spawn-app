<?php declare(strict_types = 1);
namespace SpawnCore\System\Custom\RenderExtensions\Twig\Abstracts;


use Twig\Environment;
use Twig\TwigFilter;

abstract class FilterExtension
{

    /**
     * @param Environment $twig
     */
    public function addToTwig(Environment $twig): void
    {
        $filter = new TwigFilter(
            $this->getFilterName(),
            $this->getFilterFunction(),
            $this->getFilteroptions()
        );

        $twig->addFilter($filter);
    }

    /**
     * @return string
     */
    abstract protected function getFilterName(): string;

    /**
     * @return callable
     */
    abstract protected function getFilterFunction(): callable;

    /**
     * @return array
     */
    abstract protected function getFilterOptions(): array;


}