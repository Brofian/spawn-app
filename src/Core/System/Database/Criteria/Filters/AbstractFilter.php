<?php

namespace SpawnCore\System\Database\Criteria\Filters;

abstract class AbstractFilter {

    abstract public function getCondition(): string;
    abstract public function getParameters(): array;

}