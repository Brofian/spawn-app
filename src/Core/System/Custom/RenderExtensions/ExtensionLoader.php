<?php declare(strict_types=1);

namespace SpawnCore\System\Custom\RenderExtensions;


use Doctrine\DBAL\Exception;
use SpawnCore\System\Custom\RenderExtensions\Twig\Abstracts\FilterExtension;
use SpawnCore\System\Custom\RenderExtensions\Twig\Abstracts\FunctionExtension;
use SpawnCore\System\Custom\RenderExtensions\Twig\CacheFunctionExtension;
use SpawnCore\System\Custom\RenderExtensions\Twig\DumpFunctionExtension;
use SpawnCore\System\Custom\RenderExtensions\Twig\HashFilterExtension;
use SpawnCore\System\Custom\RenderExtensions\Twig\IconFilterExtension;
use SpawnCore\System\Custom\RenderExtensions\Twig\PreviewFilterExtension;
use SpawnCore\System\Custom\Throwables\DatabaseConnectionException;
use SpawnCore\System\Database\Entity\RepositoryException;
use SpawnCore\System\ServiceSystem\ServiceContainerProvider;
use SpawnCore\System\ServiceSystem\ServiceTags;
use Twig\Environment;

class ExtensionLoader
{

    /**
     * @throws Exception
     * @throws DatabaseConnectionException
     * @throws RepositoryException
     */
    public static function loadTwigExtensions(Environment $twig): bool
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