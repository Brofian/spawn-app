<?php

namespace SpawnCore\System\ServiceSystem;

use SpawnCore\System\CardinalSystem\ModuleNetwork\ModuleLoader;
use SpawnCore\System\Custom\Gadgets\CookieHelper;
use SpawnCore\System\Custom\Gadgets\FileEditor;
use SpawnCore\System\Custom\Gadgets\Logger;
use SpawnCore\System\Custom\Gadgets\SessionHelper;
use SpawnCore\System\Custom\Gadgets\StringConverter;
use SpawnCore\System\Custom\Gadgets\XMLReader;
use SpawnCore\System\Database\Entity\EntityCollection;
use SpawnCore\System\Database\Entity\TableRepository;
use SpawnCore\System\Database\Helpers\DatabaseConnection;
use SpawnCore\System\Database\Helpers\DatabaseHelper;
use SpawnCore\System\EventSystem\EventInitializer;

class ServiceContainerProvider
{

    public const CORE_SERVICE_LIST = [
        'system.cookie.helper' => [
            ServiceProperties::_TAGS => [ServiceTags::BASE_SERVICE],
            ServiceProperties::_STATIC => true,
            ServiceProperties::_CLASS => CookieHelper::class,
        ],
        'system.session.helper' => [
            ServiceProperties::_TAGS => [ServiceTags::BASE_SERVICE],
            ServiceProperties::_STATIC => true,
            ServiceProperties::_CLASS => SessionHelper::class,
        ],
        'system.database.helper' => [
            ServiceProperties::_TAGS => [ServiceTags::BASE_SERVICE],
            ServiceProperties::_STATIC => true,
            ServiceProperties::_CLASS => DatabaseHelper::class,
        ],
        'system.database.connection' => [
            ServiceProperties::_TAGS => [ServiceTags::BASE_SERVICE],
            ServiceProperties::_STATIC => true,
            ServiceProperties::_CLASS => DatabaseConnection::class,
        ],
        'system.xml.helper' => [
            ServiceProperties::_TAGS => [ServiceTags::BASE_SERVICE],
            ServiceProperties::_STATIC => true,
            ServiceProperties::_CLASS => XMLReader::class,
        ],
        'system.file.editor.helper' => [
            ServiceProperties::_TAGS => [ServiceTags::BASE_SERVICE],
            ServiceProperties::_STATIC => true,
            ServiceProperties::_CLASS => FileEditor::class,
        ],
        'system.logger.helper' => [
            ServiceProperties::_TAGS => [ServiceTags::BASE_SERVICE],
            ServiceProperties::_STATIC => true,
            ServiceProperties::_CLASS => Logger::class,
        ],
        'system.string.converter.helper' => [
            ServiceProperties::_TAGS => [ServiceTags::BASE_SERVICE],
            ServiceProperties::_STATIC => true,
            ServiceProperties::_CLASS => StringConverter::class,
        ],
    ];

    protected static ServiceContainer $serviceContainer;


    public static function getServiceContainer(): ServiceContainer
    {
        if (!isset(self::$serviceContainer)) {
            $serviceLoader = new ServiceLoader();
            $moduleLoader = new ModuleLoader();

            /** @var EntityCollection $modules */
            $modules = $moduleLoader->loadModules();
            self::$serviceContainer = $serviceLoader->loadServices($modules);
            self::addCoreServices();
            self::addRepositoryServices();

            EventInitializer::registerSubscriberFromServices(self::$serviceContainer);
        }

        return self::$serviceContainer;
    }

    protected static function addCoreServices(): void
    {
        $propertySetterList = ServiceProperties::getPropertySetterMethods();

        foreach (self::CORE_SERVICE_LIST as $coreServiceId => $coreServiceData) {
            $service = new Service();
            $service->setId($coreServiceId);

            foreach ($propertySetterList as $property => $setterMethod) {
                if (isset($coreServiceData[$property])) {
                    $service->$setterMethod($coreServiceData[$property]);
                }
            }

            self::$serviceContainer->addService($service);
        }

    }

    protected static function addRepositoryServices(): void {
        $tableServices = self::$serviceContainer->getServicesByTag('database.table');
        foreach($tableServices as $tableService) {
            $tableClass = $tableService->getClass();
            $entityName = $tableClass::ENTITY_NAME;

            $service = new Service();
            $service->setId($entityName . '.repository');
            $service->setTags([ServiceTags::DATABASE_REPOSITORY]);
            $service->setStatic(true);
            $service->setClass(TableRepository::class);
            $service->addArgument('service', $tableService->getId());

            self::$serviceContainer->addService($service);
        }
    }
}