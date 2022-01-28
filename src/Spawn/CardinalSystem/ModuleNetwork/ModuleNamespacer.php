<?php declare(strict_types=1);

namespace spawnCore\CardinalSystem\ModuleNetwork;

/*
 * Currently unused
 */

class ModuleNamespacer
{

    public static $generatedNamespaces = [];

    /**
     * @param $rawNamespace
     * @return string
     */
    public static function hashNamespace($rawNamespace)
    {
        if(isset(self::$generatedNamespaces[$rawNamespace])) {
            return self::$generatedNamespaces[$rawNamespace];
        }
        self::$generatedNamespaces[$rawNamespace] = hash("md5", $rawNamespace);

        return self::$generatedNamespaces[$rawNamespace];
    }

}