<?php declare(strict_types=1);


namespace SpawnCore\System\Custom\Gadgets;


class URIHelper
{

    public const DEFAULT_SEPARATOR = DIRECTORY_SEPARATOR;

    public const SEPARATORS = [
        "/",
        "\\",
        "\/",
        " ",
        "\n"
    ];

    /**
     * @param string $p1
     * @param string $p2
     * @param string $seperator
     * @return string
     */
    public static function joinPaths(string $p1, string $p2, $seperator = self::DEFAULT_SEPARATOR, bool $trim = false): string
    {
        if ($trim) {
            $p1 = rtrim($p1, implode("", self::SEPARATORS));
        }
        $p2 = trim($p2, implode("", self::SEPARATORS));

        $joined = $p1 . "/" . $p2;

        self::pathifie($joined, $seperator, $trim);

        return $joined;
    }

    /**
     * @param string $string
     * @param string $seperator
     * @param bool $trim
     * @return string
     */
    public static function pathifie(string &$string, string $seperator = self::DEFAULT_SEPARATOR, bool $trim = false): string
    {
        $string = str_replace(self::SEPARATORS, $seperator, $string);

        if ($trim) {
            $string = rtrim($string, implode("", self::SEPARATORS));
        }

        return $string;
    }

    public static function joinMultiplePaths(...$paths)
    {
        if (count($paths) < 2) {
            return $paths;
        }


        $joinedPath = null;
        foreach ($paths as $path) {
            $path = rtrim($path, implode("", self::SEPARATORS));

            if ($joinedPath === null) {
                $joinedPath = $path;
            } else {
                $joinedPath .= "/" . $path;

                self::pathifie($joinedPath, self::DEFAULT_SEPARATOR, false);
            }
        }

        return $joinedPath;
    }


    /**
     * @param array $segments
     * @param string $seperator
     * @return string
     */
    public static function createPath(array $segments, string $seperator = self::DEFAULT_SEPARATOR): string
    {
        $path = "";

        foreach ($segments as $segment) {
            if ($path !== "") {
                $path .= $seperator;
            }
            $path .= $segment;
        }

        return $path;
    }

    /**
     * @param string $string
     * @return string
     */
    public static function urifie(string &$string): string
    {
        self::pathifie($string, "/");
        $string = urlencode($string);
        return $string;
    }
}