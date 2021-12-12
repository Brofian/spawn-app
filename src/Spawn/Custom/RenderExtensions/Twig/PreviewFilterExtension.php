<?php declare(strict_types=1);

namespace spawnCore\Custom\RenderExtensions\Twig;


use spawnCore\Custom\RenderExtensions\Twig\Abstracts\FilterExtension;

class PreviewFilterExtension extends FilterExtension
{

    /**
     * @return string
     */
    protected function getFilterName(): string
    {
        return "preview";
    }

    /**
     * @return callable
     */
    protected function getFilterFunction(): callable
    {
        return function ($text, int $length) {

            $trimmedText = trim(substr($text, 0, $length));

            if (strlen($text) > $length) {
                $trimmedText .= "...";
            }

            return $trimmedText;
        };
    }

    /**
     * @return array
     */
    protected function getFilterOptions(): array
    {
        return [];
    }
}