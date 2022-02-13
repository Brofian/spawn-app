<?php

namespace SpawnCore\System\Custom\Gadgets;

class Logger
{

    const logdir = ROOT . '/var/.log/';
    const accesslog = 'access-log.txt';
    const errorlog = 'error-log.txt';
    const devlog = 'dev-log.txt';


    /**
     * @param string $text
     * @param string $title
     */
    public static function writeToAccessLog(string $text, string $title = '')
    {
        $string = self::getCurrentTime();
        if ($title != '') {
            $string .= $title;
            $string .= PHP_EOL;
        }
        $string .= $text;
        $string .= PHP_EOL;

        $log = self::logdir . self::accesslog;
        if (!is_file($log)) {
            mkdir(self::logdir);
        }
        file_put_contents($log, $string, FILE_APPEND);
    }

    /**
     * @return string
     */
    public static function getCurrentTime()
    {
        return '[' . date('Y-m-d h:i:s') . '] ';
    }

    /**
     * @param string $text
     * @param string $title
     */
    public static function writeToErrorLog(string $text, string $title = '')
    {
        $string = self::getCurrentTime();
        if ($title != '') {
            $string .= $title;
        }
        $string .= PHP_EOL;
        $string .= $text;
        $string .= PHP_EOL;

        $log = self::logdir . self::errorlog;
        file_put_contents($log, $string, FILE_APPEND);
    }

    /**
     * @param string $text
     * @param string $title
     */
    public static function writeToDevlog(string $text, string $title = '')
    {
        $string = self::getCurrentTime();
        if ($title != '') {
            $string .= $title;
            $string .= PHP_EOL;
        }
        $string .= $text;
        $string .= PHP_EOL;

        $log = self::logdir . self::devlog;
        if (!is_file($log)) {
            mkdir(self::logdir);
        }
        file_put_contents($log, $string, FILE_APPEND);
    }

    /**
     * @return bool
     */
    public function clearAccessLog()
    {
        $log = self::logdir . self::accesslog;
        file_put_contents($log, '');
        return true;
    }

    /**
     * @return bool
     */
    public function clearErrorLog()
    {
        $log = self::logdir . self::errorlog;
        file_put_contents($log, '');
        return true;
    }
}