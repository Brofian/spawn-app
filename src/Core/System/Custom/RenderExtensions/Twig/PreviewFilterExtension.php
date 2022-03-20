<?php declare(strict_types=1);

namespace SpawnCore\System\Custom\RenderExtensions\Twig;


use SpawnCore\System\Custom\RenderExtensions\Twig\Abstracts\FilterExtension;

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
        return static function ($text, int $length) {

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