<?php declare(strict_types=1);

namespace SpawnCore\System\ServiceSystem;

use SpawnCore\System\Custom\Gadgets\FileEditor;

class ServiceCache
{

    public const CACHE_FILE_PATH = ROOT . '/var/cache/private/generated/services/services.php';

    public static function saveServiceCache(ServiceContainer $serviceContainer): void
    {

        $content = '<?php' . PHP_EOL . 'return [';

        $isFirstService = true;
        /** @var Service $service */
        foreach ($serviceContainer->getServices() as $service) {
            if ($isFirstService) {
                $isFirstService = false;
            } else {
                $content .= ",";
            }

            $content .= $service;
        }

        $content .= '];';

        FileEditor::createFile(self::CACHE_FILE_PATH, $content);
    }


    public static function readServiceCache(): ServiceContainer
    {
        $serviceContainer = new ServiceContainer();

        if (self::doesServiceCacheExist()) {

            /** @noinspection PhpIncludeInspection */
            $serviceArrays = include(self::CACHE_FILE_PATH);

            foreach ($serviceArrays as $serviceArray) {
                $serviceContainer->addService(Service::fromArray($serviceArray, $serviceContainer));
            }

        }

        return $serviceContainer;
    }


    public static function doesServiceCacheExist(): bool
    {
        return file_exists(self::CACHE_FILE_PATH);
    }

}