<?php

namespace SpawnCore\System\Custom\Throwables;

use bin\spawn\IO;
use Exception;
use Throwable;

abstract class AbstractException extends Exception
{

    protected array $vars = [];

    protected array $data = [];

    public function __construct($vars = [], Throwable $previous = null)
    {
        $this->vars = $vars;
        $this->data['previous'] = $previous;

        $this->collectExceptionData();

        parent::__construct(
            $this->generateMessage($vars),
            $this->getExitCode(),
            $previous
        );
    }

    protected function collectExceptionData(): void
    {
        $debugBacktrace = debug_backtrace();

        $this->data['file'] = $debugBacktrace[1]['file'];
        $this->data['line'] = $debugBacktrace[1]['line'];
        $this->data['trace'] = $debugBacktrace;
    }

    final protected function generateMessage(array $vars): string
    {
        if (IS_TERMINAL) {
            return $this->generateTerminalMessage($vars);
        }

        $template = $this->getMessageTemplate();
        foreach ($vars as $key => $value) {
            $template = str_replace('%' . $key . '%', $value, $template);
        }
        return $template;
    }

    final protected function generateTerminalMessage(array $vars): string
    {
        $template = $this->getMessageTemplate();
        foreach ($vars as $key => $value) {
            $template = str_replace('%' . $key . '%', $value, $template);
        }

        return
            IO::LIGHT_RED_TEXT . $template .
            IO::DEFAULT_TEXT . PHP_EOL;
    }

    abstract protected function getMessageTemplate(): string;

    abstract protected function getExitCode(): int;

    public function __toString(): string
    {
        return $this->generateMessage($this->vars);
    }


}