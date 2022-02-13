<?php

namespace SpawnCore\System\Database\Criteria\Filters;

class LikeFilter extends AbstractFilter {

    protected string $column;
    /** @var mixed $value */
    protected $value;

    public function __construct(string $column, $value)
    {
        if(!is_string($value)) {
            throw new InvalidFilterValueException(get_debug_type($value), self::class);
        }

        $this->column = $column;
        $this->value = $value;
    }

    public function getCondition(): string
    {
        return "($this->column LIKE ?)";
    }

    public function getParameters(): array
    {
        return [$this->value];
    }
}