<?php

namespace spawnCore\CardinalSystem\Guardian;

use Exception;
use spawnCore\Custom\Gadgets\Logger;

class Guardian {

    protected Exception $exception;

    public function handleException(Exception $exception): void {
        $this->exception = $exception;
        Logger::writeToErrorLog($exception->getTraceAsString(), $exception->getMessage());
    }

    public function getHandleResponse(): string {
        if(MODE === 'dev') {
            return $this->createPrivateResponse();
        }
        else {
            return $this->createPublicResponse();
        }
    }


    protected function createPublicResponse(): string {
        return '<b>Oops, something went wrong!</b> If this problem consists, please contact the page owner';
    }

    protected function createPrivateResponse(): string {
        $message = $this->exception->getMessage() ?? 'No error-message provided!';
        $trace = $this->exception->getTrace() ?? [];

        $response = "ERROR: <b>" . $message . "</b><br><pre>";
        $response .= '<ul>';
        foreach ($trace as $step) {
            $response .= '<li>';
            $response .= '<b>' . ($step['file'] ?? "unknown") . ":" . ($step['line'] ?? "unknown") . '</b>';
            $response .= ' in function <b>' . $step['function'] . '</b>';
            $response .= '</li>';
        }
        $response .= '</ul>';
        return $response;
    }

}