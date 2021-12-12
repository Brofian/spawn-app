<?php

namespace spawnCore\Database\Criteria\Filters;

abstract class AbstractFilter {

    abstract public function getCondition(): string;
    abstract public function getParameters(): array;

}