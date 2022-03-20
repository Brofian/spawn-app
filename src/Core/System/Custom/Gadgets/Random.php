<?php declare(strict_types=1);

namespace SpawnCore\System\Custom\Gadgets;

use Exception;

class Random
{

    public static function randomHex(int $max = PHP_INT_MAX): string
    {
        $rValue = self::randomInt($max);
        return dechex($rValue);
    }

    public static function randomInt(int $max = PHP_INT_MAX): int
    {
        try {
            return random_int(0, $max);
        } catch (Exception $e) {}

        return 0;
    }

    public static function randomBin(int $length = 32): int
    {
        $rValue = 0;
        for ($i = 0; $i < $length; $i++) {
            try {
                $rValue += random_int(0, 1);
            } catch (Exception $e) {}
            $rValue <<= 1;
        }

        return $rValue;
    }

}