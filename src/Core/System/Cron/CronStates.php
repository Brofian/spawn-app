<?php

namespace SpawnCore\System\Cron;

class CronStates {

    public const PENDING = 'pending';
    public const RUNNING = 'running';
    public const ERROR = 'error';
    public const SUCCESS = 'success';

    public const DEFAULT_STATE = self::PENDING;

}