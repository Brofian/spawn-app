<?php

namespace spawnCore\Cron;

abstract class AbstractCron {

    abstract public static function getCronTime(): string;

    abstract public function run(): void;

}