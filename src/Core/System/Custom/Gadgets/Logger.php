<?php

namespace SpawnCore\System\Custom\Gadgets;

use RuntimeException;

class Logger
{

    public const logdir = ROOT . '/var/.log/';
    public const accesslog = 'access-log.txt';
    public const errorlog = 'error-log.txt';
    public const devlog = 'dev-log.txt';


    /**
     * @param string $text
     * @param string $title
     */
    public static function writeToAccessLog(string $text, string $title = ''): void
    {
        $string = self::getCurrentTime();
        if ($title !== '') {
            $string .= $title;
            $string .= PHP_EOL;
        }
        $string .= $text;
        $string .= PHP_EOL;

        $log = self::logdir . self::accesslog;
        if (!is_file($log) && !mkdir($concurrentDirectory = self::logdir) && !is_dir($concurrentDirectory)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
        }
        file_put_contents($log, $string, FILE_APPEND);
    }


    public static function getCurrentTime(): string
    {
        return '[' . date('Y-m-d h:i:s') . '] ';
    }


    public static function writeToErrorLog(string $text, string $title = ''): void
    {
        $string = self::getCurrentTime();
        if ($title !== '') {
            $string .= $title;
        }
        $string .= PHP_EOL;
        $string .= $text;
        $string .= PHP_EOL;

        $log = self::logdir . self::errorlog;
        file_put_contents($log, $string, FILE_APPEND);
    }


    public static function writeToDevlog(string $text, string $title = ''): void
    {
        $string = self::getCurrentTime();
        if ($title !== '') {
            $string .= $title;
            $string .= PHP_EOL;
        }
        $string .= $text;
        $string .= PHP_EOL;

        $log = self::logdir . self::devlog;
        if (!is_file($log) && !mkdir($concurrentDirectory = self::logdir) && !is_dir($concurrentDirectory)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
        }
        file_put_contents($log, $string, FILE_APPEND);
    }


    public function clearAccessLog(): bool
    {
        $log = self::logdir . self::accesslog;
        file_put_contents($log, '');
        return true;
    }


    public function clearErrorLog(): bool
    {
        $log = self::logdir . self::errorlog;
        file_put_contents($log, '');
        return true;
    }
}