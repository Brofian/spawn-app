<?php

namespace spawnCore\Database\Criteria\Filters;

class GreaterThanFilter extends AbstractComparisonFilter {

    public function __construct(string $column, $value)
    {
        parent::__construct($column, '>', $value);
    }

}