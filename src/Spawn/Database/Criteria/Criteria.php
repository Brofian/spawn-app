<?php

namespace spawnCore\Database\Criteria;

use spawnCore\Database\Criteria\Filters\AbstractFilter;

class Criteria {

    protected ?AbstractFilter $filter;

    public function __construct(?AbstractFilter $filter = null)
    {
        $this->filter = $filter;
    }


    public function getFilters(): array {
        return [$this->filter];
    }


    public function generateCriteria(): string {
        return '(' . $this->filter->getCondition() . ')';
    }



}