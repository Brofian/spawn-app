<?php

namespace SpawnCore\System\Custom\Gadgets;

class Debugger
{

    /**
     * dumps the variable and continue execution
     * @param $var
     */
    public static function dump($var): void
    {
        if (MODE !== 'dev') {
            self::officialDump();
        }

        self::writeBacktrace($var, debug_backtrace());
    }

    /**
     * The official version. This is shown, if the MODE is not set to "dev" in the config
     */
    public static function officialDump(): void
    {
        echo "Something went wrong!";
        die();
    }

    /**
     * the format for dumping a variable
     * @param $var
     * @param $backtrace
     */
    private static function writeBacktrace($var, $backtrace): void
    {
        if (MODE !== 'dev') {
            self::officialDump();
        }

        echo "  <div style='background: #FFAAAA; border: 2px solid black; padding:10px'>
                    At: <b>" . $backtrace[0]["file"] . ":" . $backtrace[0]["line"] . "</b>
                    <pre>";
        var_dump($var);
        echo "</pre></div>";
    }

    /**
     * dumps the variable and dies
     * @param $var
     */
    public static function ddump($var): void
    {
        if (MODE !== 'dev') {
            self::officialDump();
        }

        self::writeBacktrace($var, debug_backtrace());
        die();
    }

    /**
     * dumps the variable and dies, used for strings
     * @param string $var
     */
    public static function sdump(string $var): void
    {
        if (MODE !== 'dev') {
            self::officialDump();
        }

        $backtrace = debug_backtrace();

        echo "<div style='background: #FFAAAA; border: 2px solid black; padding:10px'>";
        echo "At: <b>" . $backtrace[0]["file"] . ":" . $backtrace[0]["line"] . "</b><br><br>";
        echo($var);
        echo "</div>";

    }

}