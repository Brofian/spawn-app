<?php

namespace SpawnCore\System\Database\Criteria\Filters;

class RawFilter extends AbstractFilter {

    protected string $column;
    protected string $condition;

    public function __construct(string $column, string $condition)
    {
        $this->column = $column;
        $this->condition = $condition;
    }

    public function getCondition(): string
    {
        return "($this->column $this->condition)";
    }

    public function getParameters(): array
    {
        return [];
    }
}