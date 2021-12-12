<?php declare(strict_types=1);


namespace spawnCore\Custom\Gadgets;


class URIHelper
{

    const DEFAULT_SEPERATOR = DIRECTORY_SEPARATOR;

    const SEPERATORS = [
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
    public static function joinPaths(string $p1, string $p2, $seperator = self::DEFAULT_SEPERATOR, bool $trim = false)
    {
        if ($trim) {
            $p1 = rtrim($p1, implode("", self::SEPERATORS));
        }
        $p2 = trim($p2, implode("", self::SEPERATORS));

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
    public static function pathifie(string &$string, string $seperator = self::DEFAULT_SEPERATOR, bool $trim = false): string
    {
        $string = str_replace(self::SEPERATORS, $seperator, $string);

        if ($trim) {
            $string = rtrim($string, implode("", self::SEPERATORS));
        }

        return $string;
    }

    public static function joinMultiplePaths(...$paths)
    {
        if (count($paths) < 2) return $paths;


        $joinedPath = null;
        foreach ($paths as $path) {
            $path = rtrim($path, implode("", self::SEPERATORS));

            if ($joinedPath == null) {
                $joinedPath = $path;
            } else {
                $joinedPath .= "/" . $path;

                self::pathifie($joinedPath, self::DEFAULT_SEPERATOR, false);
            }
        }

        return $joinedPath;
    }


    /**
     * @param array $segments
     * @param string $seperator
     * @return string
     */
    public static function createPath(array $segments, string $seperator = self::DEFAULT_SEPERATOR)
    {
        $path = "";

        foreach ($segments as $segment) {
            if ($path != "") {
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
    public static function urifie(string &$string)
    {
        self::pathifie($string, "/");
        $string = urlencode($string);
        return $string;
    }
}