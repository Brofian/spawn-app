<?php declare(strict_types=1);

namespace spawnCore\Custom\Gadgets;

class Random
{

    public static function randomHex(int $max = PHP_INT_MAX)
    {
        $rValue = self::randomInt($max);
        return dechex($rValue);
    }

    public static function randomInt(int $max = PHP_INT_MAX)
    {
        return rand(0, PHP_INT_MAX);
    }

    public static function randomBin(int $length = 32)
    {
        $rValue = 0;
        for ($i = 0; $i < $length; $i++) {
            $rValue += rand(0, 1);
            $rValue <<= 1;
        }

        return $rValue;
    }

}