<?php

namespace spawnCore\Cron;

abstract class AbstractCron {

    protected string $output = '';

    abstract public static function getCronTime(): string;

    abstract public function run(): int;

    protected function addOutput(string $output): void {
        $this->output .= $output . PHP_EOL;
    }

    protected function addInfo(string $info): void {
        $this->addOutput(
            (new \DateTime())->format('Y-m-d h:i:s:v') . ' :: [INFO] :: ' . $info . PHP_EOL
        );
    }

    public function getOutput(): string {
        return $this->output;
    }

}