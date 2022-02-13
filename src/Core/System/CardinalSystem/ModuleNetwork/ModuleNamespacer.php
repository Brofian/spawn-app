<?php declare(strict_types=1);

namespace SpawnCore\System\CardinalSystem\ModuleNetwork;

/*
 * Currently unused
 */

class ModuleNamespacer
{
    public const FALLBACK_NAMESPACE = 'raw';

    public static array $generatedNamespaces = [];

    /**
     * @param $rawNamespace
     * @return string
     */
    public static function hashNamespace($rawNamespace): string
    {
        if(isset(self::$generatedNamespaces[$rawNamespace])) {
            return self::$generatedNamespaces[$rawNamespace];
        }
        self::$generatedNamespaces[$rawNamespace] = hash("md5", $rawNamespace);

        return self::$generatedNamespaces[$rawNamespace];
    }

}