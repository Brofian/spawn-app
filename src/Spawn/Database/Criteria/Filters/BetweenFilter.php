<?php

namespace spawnCore\Database\Criteria\Filters;

class BetweenFilter extends AbstractFilter {

    protected string $column;
    /** @var mixed */
    protected $min;
    /** @var mixed */
    protected $max;

    public function __construct(string $column, $min, $max)
    {
        if(!is_string($min) && !is_numeric($min)) {
            throw new InvalidFilterValueException(get_debug_type($min), self::class);
        }
        if(!is_string($max) && !is_numeric($max)) {
            throw new InvalidFilterValueException(get_debug_type($max), self::class);
        }


        $this->column = $column;
        $this->min = $min;
        $this->max = $max;
    }

    public function getCondition(): string
    {
        return "($this->column BETWEEN ? and ?)";
    }

    public function getParameters(): array
    {
        return [$this->min, $this->max];
    }
}