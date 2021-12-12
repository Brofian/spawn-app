<?php

namespace spawnCore\Custom\Gadgets;

class Slugifier
{

    public const SYSTEM_SLUGS = [
        'SpawnApp'
    ];


    public static function isSystemSlug(string $slug): bool
    {
        return in_array($slug, self::SYSTEM_SLUGS);
    }

    /**
     * transforms string to camelCase
     * @param string $subjectString
     * @return string
     */
    public static function toCamelCase(string $subjectString): string
    {
        $subjectString = self::slugify($subjectString);
        return lcfirst($subjectString);
    }

    /**
     * transforms string to PascalCase
     * @param string $subjectString
     * @return string
     */
    public static function slugify(string $subjectString): string
    {
        return self::toPascalCase($subjectString);
    }

    /**
     * transforms string to PascalCase
     * @param string $subjectString
     * @return string
     */
    public static function toPascalCase(string $subjectString): string
    {
        $subjectString = str_replace(['_', '-', '/', '\\', '.', ','], ' ', $subjectString);
        $subjectString = ucwords($subjectString);
        $subjectString = str_replace(' ', '', $subjectString);

        return $subjectString;
    }

    /**
     * transforms string to kebap-case
     * @param string $subjectString
     * @return string
     */
    public static function toKebapCase(string $subjectString): string
    {
        $words = preg_split('/(?=[A-Z])/', $subjectString);
        $subjectString = implode('-', $words);

        $subjectString = str_replace([' ', '_', '/', '\\', '.', ','], '-', $subjectString);
        return strtolower($subjectString);
    }

    /**
     * transforms string to UPPERCASE_SNAKE_CASE
     * @param string $subjectString
     * @return string
     */
    public static function toUppercaseSnakeCase(string $subjectString): string
    {
        return strtoupper(self::toSnakeCase($subjectString));
    }

    /**
     * transforms string to snake_case
     * @param string $subjectString
     * @return string
     */
    public static function toSnakeCase(string $subjectString): string
    {
        $words = preg_split('/(?=[A-Z])/', $subjectString);
        $subjectString = implode('_', $words);

        $subjectString = str_replace([' ', '-', '/', '\\', '.', ','], '_', $subjectString);
        return strtolower($subjectString);
    }


}