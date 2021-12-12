<?php declare(strict_types=1);

namespace spawnCore\CardinalSystem\ModuleNetwork;

/*
 * Currently unused
 */

class ModuleNamespacer
{

    public const GLOBAL_NAMESPACE_RAW = "global";

    /**
     * @param $rawNamespace
     * @return string
     */
    public static function hashNamespace($rawNamespace)
    {
        return hash("md5", $rawNamespace);
    }

}