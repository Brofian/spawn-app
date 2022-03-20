<?php declare(strict_types = 1);
namespace SpawnCore\System\Custom\Response;

use SpawnCore\System\Custom\Gadgets\TwigHelper;
use SpawnCore\System\ServiceSystem\ServiceContainerProvider;

class TwigResponse extends AbstractResponse
{

    protected TwigHelper $twig;

    protected string $renderFilePath = 'base.html.twig';
    protected ?array $twigData = null;

    public function __construct(string $renderFilePath, ?array $twigData = null, ?CacheControlState $cache = null)
    {
        parent::__construct('', $cache);
        $this->twig = ServiceContainerProvider::getServiceContainer()->getServiceInstance('system.twig.helper');
        $this->renderFilePath = $renderFilePath;
        $this->twigData = $twigData;
    }


    public function getResponse(): string
    {
        return $this->twig->render($this->renderFilePath, $this->twigData);
    }

}