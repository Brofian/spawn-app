<?php

namespace spawnCore\Database\Criteria\Filters;

class NotFilter extends AbstractFilter {

    protected AbstractFilter $filter;

    public function __construct(AbstractFilter $filter)
    {
        $this->filter = $filter;
    }

    public function getFilters(): array {
        return [$this->filter];
    }

    public function getCondition(): string
    {
        return '(NOT ' . $this->filter->getCondition() . ')';
    }

    public function getParameters(): array
    {
        return $this->filter->getParameters();
    }
}