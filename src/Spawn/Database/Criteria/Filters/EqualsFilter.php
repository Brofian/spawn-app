<?php

namespace spawnCore\Database\Criteria\Filters;

class EqualsFilter extends AbstractFilter {

    protected string $column;
    /** @var mixed $value */
    protected $value;

    public function __construct(string $column, $value)
    {
        if(!is_string($value) && !is_numeric($value) && !is_bool($value)) {
            throw new InvalidFilterValueException(get_debug_type($value), self::class);
        }

        $this->column = $column;
        $this->value = $value;
    }

    public function getCondition(): string
    {
        return "($this->column = ?)";
    }

    public function getParameters(): array
    {
        return [$this->value];
    }
}