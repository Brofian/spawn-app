<?php

namespace spawnCore\CardinalSystem\Guardian;

use Error;
use Exception;
use spawnCore\Custom\Gadgets\Logger;
use Throwable;

class Guardian {

    protected Exception $exception;

    public function handleException(Throwable $exception): void {
        if(!$exception instanceof Exception) {
            /** @var Error $exception */
            $exception = new Exception($exception->getMessage(), $exception->getCode(), $exception);
        }

        $this->exception = $exception;
        Logger::writeToErrorLog($this->getFullTrace($exception), $exception->getMessage());
    }

    protected function getFullTrace(Exception $exception): string {
        $trace = $exception->getTraceAsString();
        while ($exception->getPrevious() instanceof Exception || $exception->getPrevious() instanceof Error) {
            $exception = $exception->getPrevious();
            $trace .= PHP_EOL . PHP_EOL . 'Previous Exception "'.$exception->getMessage().'"'.PHP_EOL;
            $trace .= $exception->getTraceAsString();
        }

        return $trace;
    }

    public function getHandleResponse(): string {
        if(MODE === 'dev') {
            return $this->createPrivateResponse($this->exception);
        }
        else {
            return $this->createPublicResponse();
        }
    }


    protected function createPublicResponse(): string {
        return '<b>Oops, something went wrong!</b> If this problem consists, please contact the page owner';
    }

    /**
     * @param Exception|Error $e
     * @return string
     */
    protected function createPrivateResponse($e): string {
        $message = $e->getMessage() ?? 'No error-message provided!';
        $trace = $e->getTrace() ?? [];

        $response = "ERROR: <b>" . $message . "</b><br><pre>";
        $response .= '<ul>';
        foreach ($trace as $step) {
            $response .= '<li>';
            $response .= '<b>' . ($step['file'] ?? "unknown") . ":" . ($step['line'] ?? "unknown") . '</b>';
            $response .= ' in function <b>' . $step['function'] . '</b>';
            $response .= '</li>';
        }
        $response .= '</ul>';

        if($e->getPrevious() instanceof Exception || $e->getPrevious() instanceof Error) {
            $response .= '<br><hr><br>';
            $response .= $this->createPrivateResponse($e->getPrevious());
        }

        return $response;
    }

}