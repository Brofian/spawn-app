<?php declare(strict_types = 1);
namespace SpawnCore\System\Custom\Gadgets;

class Stopwatch
{

    public static float $startingTime = 0;

    public static function start(): void
    {
        self::$startingTime = floor(microtime(true) * 1000);
    }

    public static function end(int $precision = 5): string
    {
        $endingTime = microtime(true) * 1000;
        $duration = $endingTime - self::$startingTime;
        $unit = self::toHumanReadableTime($duration);
        return round($duration, $precision) . $unit;
    }

    public static function toHumanReadableTime(float &$milliseconds): string
    {
        if ($milliseconds < 2000) { //2s
            return 'ms';
        }

        if ($milliseconds < 60000) { //1m
            $milliseconds /= 1000;
            return 's';
        }

        $milliseconds /= 60000;
        return 'm';
    }

}