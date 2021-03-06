<?php declare(strict_types = 1);
namespace SpawnBackend\Extensions\Twig;

use SpawnCore\Defaults\Services\SnippetManager;
use SpawnCore\System\Custom\RenderExtensions\Twig\Abstracts\FilterExtension;
use SpawnCore\System\ServiceSystem\ServiceContainerProvider;

class TranslateFilter extends FilterExtension {


    protected function getFilterName(): string
    {
        return 'translate';
    }

    protected function getFilterFunction(): callable
    {
        return (static function(string $path, ?string $language = null) {
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