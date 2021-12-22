<?php

namespace spawnCore\Database\Criteria\Filters;

class AndFilter extends AbstractFilter {

    /** @var AbstractFilter[]  */
    protected array $filters = [];

    public function __construct(AbstractFilter $filter1, AbstractFilter ...$filters)
    {
        $this->addFilter($filter1, ...$filters);
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

        return '(' . implode(' AND ', $conditions) . ')';
    }

    public function getParameters(): array
    {
        $parameters = [];
        foreach ($this->filters as $filter) {
            $parameters = array_merge($parameters, $filter->getParameters());
        }
        return $parameters;
    }
}