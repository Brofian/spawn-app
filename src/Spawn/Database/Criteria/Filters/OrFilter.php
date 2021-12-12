<?php

namespace spawnCore\Database\Criteria\Filters;

class OrFilter extends AbstractFilter {

    /** @var AbstractFilter[]  */
    protected array $filters = [];

    public function __construct(AbstractFilter ...$filters)
    {
        $this->addFilter(...$filters);
    }

    public function addFilter(AbstractFilter ...$filters)
    {
        foreach($filters as $filter) {
            $this->filters[] = $filter;
        }
    }

    public function getFilters(): array {
        return $this->filters;
    }

    public function getCondition(): string
    {
        $conditions = [];

        foreach($this->filters as $filter) {
            $conditions[] = $filter->getCondition();
        }

        return '(' . implode(' OR ', $conditions) . ')';
    }

    public function getParameters(): array
    {
        $parameters = [];
        foreach ($this->filters as $filter) {
            $parameters = array_merge($filter->getParameters());
        }
        return $parameters;
    }
}