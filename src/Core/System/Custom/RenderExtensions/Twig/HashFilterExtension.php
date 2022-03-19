<?php declare(strict_types = 1);
namespace SpawnCore\System\Custom\RenderExtensions\Twig;


use SpawnCore\System\Custom\RenderExtensions\Twig\Abstracts\FilterExtension;

class HashFilterExtension extends FilterExtension
{

    /**
     * @return string
     */
    protected function getFilterName(): string
    {
        return "hash";
    }

    /**
     * @return callable
     */
    protected function getFilterFunction(): callable
    {
        return static function ($string, $hashtype = "md5") {
            return hash($hashtype, $string);
        };
    }

    /**
     * @return array
     */
    protected function getFilterOptions(): array
    {
        return [
            'is_safe' => ['html']
        ];
    }
}