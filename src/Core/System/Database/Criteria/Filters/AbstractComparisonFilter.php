<?php

namespace SpawnCore\System\Database\Criteria\Filters;

abstract class AbstractComparisonFilter extends AbstractFilter {

    protected string $comparison;
    protected string $column;
    /** @var mixed $value */
    protected $value;

    public const ALLOWED_COMPARISONS = [
        '<',
        '<=',
        '>',
        '>=',
    ];

    /**
     * @throws InvalidFilterValueException
     */
    public function __construct(string $column, string $comparison, $value)
    {
        if(!is_string($value) && !is_numeric($value)) {
            throw new InvalidFilterValueException(get_debug_type($value), static::class);
        }

        if(!in_array($comparison, self::ALLOWED_COMPARISONS, true)) {
            throw new InvalidFilterValueException(get_debug_type($value), self::class);
        }

        $this->column = $column;
        $this->value = $value;
    }

    public function getCondition(): string
    {
        return "($this->column $this->comparison ?)";
    }

    public function getParameters(): array
    {
        return [$this->value];
    }
}