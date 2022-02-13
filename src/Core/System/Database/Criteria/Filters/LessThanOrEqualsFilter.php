<?php

namespace SpawnCore\System\Database\Criteria\Filters;

class LessThanOrEqualsFilter extends AbstractComparisonFilter {

    public function __construct(string $column, $value)
    {
        parent::__construct($column, '<=', $value);
    }

}