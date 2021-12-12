<?php declare(strict_types=1);

namespace spawnCore\CardinalSystem;

use spawnCore\Custom\Gadgets\Logger;
use Throwable;

/*
 * The Main Environment to handle the system
 */

class Environment
{

    protected Kernel $kernel;


    public function __construct()
    {
        $this->kernel = new Kernel();
    }

    public function handle()
    {
        try {
            $this->kernel->handle();
            return $this->kernel->getAnswer();

        } catch (Throwable $exception) {
            $this->handleException($exception);
            return "This can never be reached, because handleException ends with a die()";
        }
    }


    private function handleException(Throwable $exception)
    {

        Logger::writeToErrorLog($exception->getTraceAsString(), $exception->getMessage());

        if (MODE == 'dev') {
            $message = $exception->getMessage() ?? 'No error-message provided!';
            $trace = $exception->getTrace() ?? [];

            echo "ERROR: <b>" . $message . "</b><br><pre>";

            echo "<ul>";
            foreach ($trace as $step) {
                echo "<li>";
                echo "<b>" . ($step['file'] ?? "unknown") . ":" . ($step['line'] ?? "unknown") . "</b>";
                echo " in function <b>" . $step['function'] . "</b>";
            }
            echo "</ul>";

            var_dump($exception);
        } else {
            echo "Leider ist etwas schief gelaufen :(";
        }

        die();
    }


}