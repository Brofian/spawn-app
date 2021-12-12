<?php declare(strict_types=1);

namespace spawnCore\Custom\RenderExtensions\Twig;


use spawnCore\Custom\RenderExtensions\Twig\Abstracts\FilterExtension;

class IconFilterExtension extends FilterExtension
{

    /**
     * @return string
     */
    protected function getFilterName(): string
    {
        return "icon";
    }

    /**
     * @return callable
     */
    protected function getFilterFunction(): callable
    {
        return function ($icon, $namespace = 'SpawnApp', $additionalClasses = '') {

            $iconPath = ROOT . '/public/pack/' . $namespace . '/icons/' . $icon . '.svg';

            if (!file_exists($iconPath)) {
                if (MODE == 'dev') return "Icon \"" . $iconPath . "\" not found!";
                else                return "Missing icon";
            }

            $svgFile = file_get_contents($iconPath);
            $svgFile = "<span class='icon " . $additionalClasses . "'>" . $svgFile . "</span>";
            return $svgFile;
        };
    }

    /**
     * @return array
     */
    protected function getFilterOptions(): array
    {
        return [
            'needs_context' => false,
            'is_safe' => ['html']
        ];
    }
}