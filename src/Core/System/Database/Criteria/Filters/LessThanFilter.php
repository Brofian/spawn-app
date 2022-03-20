<?php declare(strict_types = 1);
namespace SpawnCore\System\Database\Criteria\Filters;

class LessThanFilter extends AbstractComparisonFilter {

    public function __construct(string $column, $value)
    {
        parent::__construct($column, '<', $value);
    }

}