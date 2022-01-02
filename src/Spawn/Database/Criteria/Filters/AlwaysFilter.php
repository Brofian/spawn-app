<?php

namespace spawnCore\Database\Criteria\Filters;

class AlwaysFilter extends AbstractFilter {

    protected int $value;

    public function __construct(bool $value = true)
    {
        $this->value = ($value) ? 1 : 0;
    }

    public function getCondition(): string
    {
        return '('.$this->value.')';
    }

    public function getParameters(): array
    {
        return [];
    }
}