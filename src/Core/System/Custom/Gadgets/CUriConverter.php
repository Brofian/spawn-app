<?php

namespace SpawnCore\System\Custom\Gadgets;

/*
 * Custom Uri Converted
 * Allows reading and converting custom urls (like "/foo/bar/{id}/test")
 * to normal urls
 */

class CUriConverter
{

    /**
     * @param string $cUri
     * @param array $parameters
     * @return string|string[]
     */
    public static function cUriToUri(string $cUri, array $parameters)
    {

        foreach ($parameters as $value) {
            $cUri = preg_replace('/{}/', $value, $cUri, 1);
        }

        return $cUri;
    }

    /**
     * @param string $uri
     * @param array $vars
     * @return string|string[]
     */
    public static function cUriToRegex(string $uri)
    {
        $uri = "/" . trim($uri, "/ \n");
        $uri = str_replace('/', "\/", $uri);
        $uri = str_replace('{}', "([^\/]*)", $uri);
        $uri = "/^" . $uri . "$/m";

        return $uri;
    }


    public static function getParametersFromUri(string $uri, string $curi): array
    {
        $uri = "/" . $uri;

        $matches = [];
        preg_match_all($curi, $uri, $matches);

        $parameters = [];
        for ($i = 1, $iMax = count($matches); $i < $iMax; $i++) {
            $parameters[] = $matches[$i][0];
        }

        return $parameters;
    }


    public static function getParameterNames(string $c_uri): array
    {

        $pattern = "/{([^}]*)}/m";
        preg_match_all($pattern, $c_uri, $matches);

        return $matches[1] ?? [];
    }


}