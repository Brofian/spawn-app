<?php

namespace SpawnCore\System\Database\Criteria\Filters;

class IsNullFilter extends AbstractFilter {

    protected string $column;

    public function __construct(string $column)
    {
        $this->column = $column;
    }

    public function getCondition(): string
    {
        return "($this->column IS NULL)";
    }

    public function getParameters(): array
    {
        return [];
    }
}