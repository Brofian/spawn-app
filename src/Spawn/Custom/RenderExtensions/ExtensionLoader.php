<?php declare(strict_types=1);

namespace spawnCore\Custom\RenderExtensions;


use spawnCore\Custom\RenderExtensions\Twig\Abstracts\FilterExtension;
use spawnCore\Custom\RenderExtensions\Twig\Abstracts\FunctionExtension;
use spawnCore\Custom\RenderExtensions\Twig\AssetFunctionExtension;
use spawnCore\Custom\RenderExtensions\Twig\CacheFunctionExtension;
use spawnCore\Custom\RenderExtensions\Twig\DumpFunctionExtension;
use spawnCore\Custom\RenderExtensions\Twig\HashFilterExtension;
use spawnCore\Custom\RenderExtensions\Twig\IconFilterExtension;
use spawnCore\Custom\RenderExtensions\Twig\PreviewFilterExtension;
use spawnCore\ServiceSystem\ServiceContainerProvider;
use spawnCore\ServiceSystem\ServiceTags;
use Twig\Environment;

class ExtensionLoader
{

    /**
     * @param Environment $twig
     * @return bool
     */
    public static function loadTwigExtensions(Environment &$twig)
    {

        //add twig extensions from modules
        $twigExtensions = ServiceContainerProvider::getServiceContainer()->getServicesByTag(ServiceTags::EXTENSION_TWIG);
        foreach ($twigExtensions as $twigExtension) {
            /** @var FilterExtension|FunctionExtension $instance */
            $instance = $twigExtension->getInstance();
            $instance->addToTwig($twig);
        }

        /*
         * Filter
         */
        $hashFilter = new HashFilterExtension();
        $hashFilter->addToTwig($twig);

        $iconFilter = new IconFilterExtension();
        $iconFilter->addToTwig($twig);

        $previewFilter = new PreviewFilterExtension();
        $previewFilter->addToTwig($twig);


        /*
         * Functions
         */
        $assetFunction = new AssetFunctionExtension();
        $assetFunction->addToTwig($twig);

        $cacheFunction = new CacheFunctionExtension();
        $cacheFunction->addToTwig($twig);

        $dumpFunction = new DumpFunctionExtension();
        $dumpFunction->addToTwig($twig);


        /*
         * Tags
         */


        return true;
    }
}