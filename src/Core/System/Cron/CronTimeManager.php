<?php

namespace SpawnCore\System\Cron;


use DateTime;
use Exception;
use SpawnCore\System\Custom\Throwables\InvalidCronPatternException;

class CronTimeManager {

    protected DateTime $now;

    protected const METHOD_ALLOW_ALL = 0;
    protected const METHOD_TIMESPAN = 1;
    protected const METHOD_INTERVAL = 2;
    protected const METHOD_MATCH = 3;

    protected const DATETIME_FORMATS = [
        'minute' => 'i',
        'hour' => 'H',
        'month' => 'm',
        'dayOfMonth' => 'd',
        'dayOfWeek' => 'w',
    ];

    public function __construct()
    {
        $this->now = new DateTime();
    }

    public function shouldCronBeExecuted(string $cronPattern): bool {

        try {
            $cronTimings = $this->interpretCronPattern($cronPattern);
        }
        catch (Exception $e) {
            return false;
        }


        foreach($cronTimings as $type => $cronTiming) {
            $format = self::DATETIME_FORMATS[$type];
            $timeValue = $this->now->format($format);

            switch($cronTiming['method']) {
                case self::METHOD_ALLOW_ALL:
                    //if the method is allow_all, just ignore this check and continue
                    break;
                case self::METHOD_TIMESPAN:
                    //if the method is timespan, check if the current time is in the timespan. Abort if not
                    if($timeValue < $cronTiming['from'] || $timeValue > $cronTiming['to']) {
                        return false;
                    }
                    break;
                case self::METHOD_INTERVAL:
                    //if the method is interval, check if the current time is this interval
                    if($timeValue % $cronTiming['divider'] !== 0) {
                        return false;
                    }
                    break;
                case self::METHOD_MATCH:
                    //if the method is interval, check if the current time is this interval
                    if($timeValue != $cronTiming['divider']) {
                        return false;
                    }
                    break;
            }

        }

        // at this point, all criteria are valid, so just return true
        return true;
    }

    /**
     * @throws InvalidCronPatternException
     */
    protected function interpretCronPattern(string $cronPattern): array {

        $parts = explode(' ', $cronPattern);
        if(count($parts) != 5) {
            throw new InvalidCronPatternException($cronPattern);
        }

        $minute = $this->interpretTimeDefinition($parts[0], 0, 59);
        $hour = $this->interpretTimeDefinition($parts[1], 0, 23);
        $dayOfMonth = $this->interpretTimeDefinition($parts[2], 1, 31);
        $month = $this->interpretTimeDefinition($parts[3], 1, 12);
        $dayOfWeek = $this->interpretTimeDefinition($parts[4], 0, 6);

        if(!isset($minute, $hour, $dayOfMonth, $month, $dayOfWeek)) {
            throw new InvalidCronPatternException($cronPattern);
        }

        return [
            'minute' => $minute,
            'hour' => $hour,
            'dayOfMonth' => $dayOfMonth,
            'month' => $month,
            'dayOfWeek' => $dayOfWeek,
        ];

    }


    protected function interpretTimeDefinition(string $timeDefinition, int $min, int $max): ?array {
        if($timeDefinition === '*') {
            return [
                'method' => self::METHOD_ALLOW_ALL
            ];
        }
        elseif(preg_match('/^(\d+)-(\d+)$/m', $timeDefinition, $matches)) {
            if($matches[1] >= $min && $matches[1] <= $max && $matches[2] >= $min && $matches[2] <= $max) {
                return [
                    'method' => self::METHOD_TIMESPAN,
                    'from' => $matches[1],
                    'to' => $matches[2],
                ];
            }

        }
        elseif(preg_match('/^\*\/(\d+)$/m', $timeDefinition, $matches)) {
            if($matches[1] >= $min && $matches[1] <= $max) {
                return [
                    'method' => self::METHOD_INTERVAL,
                    'divider' => $matches[1]
                ];
            }
        }
        elseif(preg_match('/^(\d+)$/m', $timeDefinition, $matches)) {
            if($matches[1] >= $min && $matches[1] <= $max) {
                return [
                    'method' => self::METHOD_MATCH,
                    'match' => $matches[1]
                ];
            }
        }

        return null;
    }


}
