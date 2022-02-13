<?php

namespace SpawnCore\System\Database\Criteria;

use SpawnCore\System\Database\Criteria\Filters\AbstractFilter;
use SpawnCore\System\Database\Criteria\Filters\AndFilter;

class Criteria {

    /** @var AbstractFilter[]  */
    protected array $filters = [];

    public function __construct(AbstractFilter ...$filters)
    {
        foreach($filters as $filter) {
            $this->filters[] = $filter;
        }
    }


    public function getFilters(): array {
        return $this->filters;
    }

    public function addFilter(AbstractFilter $filter): void {
        $this->filters[] = $filter;
    }

    public function getParameters(): array {
        $params = [];
        foreach($this->filters as $filter) {
            $params = array_merge($params, $filter->getParameters());
        }

        return $params;
    }

    public function generateCriteria(): string {
        if(!empty($this->filters)) {
            $combinedFilter = new AndFilter(...$this->filters);
            return $combinedFilter->getCondition();
        }
        return '(1)';
    }



}