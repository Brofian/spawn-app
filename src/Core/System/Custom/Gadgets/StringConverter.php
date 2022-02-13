<?php declare(strict_types=1);

namespace SpawnCore\System\Custom\Gadgets;

class StringConverter
{


    public static function snakeToPascalCase($string): string
    {
        $string = strtolower($string);

        $stringParts = explode("-", $string);
        $string = "";
        foreach ($stringParts as $part) {
            $string .= ucFirst($part);
        }
        return $string;
    }

    public static function pascalToSnakeCase($string): string
    {
        $letters = str_split($string);
        $string = "";
        foreach ($letters as $letter) {
            if ($string === "" || ctype_lower($letter)) {
                $string .= $letter;
            } else {
                $string .= "-" . $letter;
            }
        }

        return strtolower($string);
    }


}