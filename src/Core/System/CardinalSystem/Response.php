<?php declare(strict_types=1);

namespace SpawnCore\System\CardinalSystem;


/*
 *  The Main Class to store all Response informations
 */


use SpawnCore\System\Custom\Gadgets\HeaderHelper;
use SpawnCore\System\Custom\Gadgets\ResourceCollector;
use SpawnCore\System\Custom\Gadgets\ScssHelper;
use SpawnCore\System\Custom\Gadgets\TwigHelper;
use SpawnCore\System\Custom\Gadgets\URIHelper;
use SpawnCore\System\Custom\Response\AbstractResponse;
use SpawnCore\System\Custom\Throwables\HeadersSendByException;
use SpawnCore\System\Database\Entity\EntityCollection;
use SpawnCore\System\ServiceSystem\ServiceContainerProvider;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class Response
{

    protected string $html = '';
    protected TwigHelper $twigHelper;
    protected ScssHelper $scssHelper;
    protected HeaderHelper $headerHelper;
    protected EntityCollection $moduleCollection;
    protected AbstractResponse $responseObject;

    public function __construct()
    {
        $serviceContainer = ServiceContainerProvider::getServiceContainer();
        $this->twigHelper = $serviceContainer->getServiceInstance('system.twig.helper');
        $this->scssHelper = $serviceContainer->getServiceInstance('system.scss.helper');
        $this->moduleCollection = $serviceContainer->getServiceInstance('system.modules.collection');
        $this->headerHelper = $serviceContainer->getServiceInstance('system.header.helper');

        $this->fillBaseContextData();
    }

    protected function fillBaseContextData()
    {
        $this->twigHelper->assign("environment", MODE);
        $this->scssHelper->setBaseVariable("assetsPath", URIHelper::createPath([
            CACHE_DIR, "public", "assets"
        ], "/"));
    }


    public function prepareFiles()
    {

        //gather resources from the modules
        if (ResourceCollector::isGatheringNeeded() || MODE == 'dev') {
            $resourceCollector = new ResourceCollector();
            $resourceCollector->gatherModuleData($this->moduleCollection);
        }

        /* Render Scss */
        if (!$this->scssHelper->cacheExists() || MODE == 'dev') {
            $this->scssHelper->createCss();
        }

    }

    /**
     * @param AbstractResponse $responseObject
     */
    public function setResponseObject(AbstractResponse $responseObject)
    {
        $this->responseObject = $responseObject;
    }

    /**
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws HeadersSendByException
     */
    public function finish(): string
    {
        if (isset($this->responseObject)) {
            $this->headerHelper->setHeader('Cache-control: ' . $this->responseObject->getCacheStatus()->getCacheControlValue(), true);

            return $this->responseObject->getResponse();
        }

        /* Render twig */
        return $this->twigHelper->finish();
    }

}