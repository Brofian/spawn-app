<?php

namespace spawnApp\Extensions\Twig;

use spawnApp\Services\SnippetManager;
use spawnCore\Custom\RenderExtensions\Twig\Abstracts\FilterExtension;
use spawnCore\ServiceSystem\ServiceContainerProvider;

class TranslateFilter extends FilterExtension {


    protected function getFilterName(): string
    {
        return 'translate';
    }

    protected function getFilterFunction(): callable
    {
        return (function(string $path, ?string $language = null) {
            $container = ServiceContainerProvider::getServiceContainer();
            /** @var SnippetManager $snippetManager */
            $snippetManager = $container->getServiceInstance('system.service.snippet_manager');
            return $snippetManager->getSnippet($path, $language ?? SnippetManager::$language);
        });
    }

    protected function getFilterOptions(): array
    {
        return [
            'is_safe' => ['html']
        ];
    }
}