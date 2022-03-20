<?php declare(strict_types = 1);
namespace SpawnCore\System\Database\Criteria\Filters;

class InFilter extends AbstractFilter {

    protected string $column;
    protected array $values;

    public function __construct(string $column, array $values)
    {
        $this->column = $column;
        $this->values = $values;
    }

    public function getCondition(): string
    {
        $placeholders = str_repeat('?,', count($this->values));
        return "($this->column IN (" . rtrim($placeholders, ',') . '))';
    }

    public function getParameters(): array
    {
        return $this->values;
    }
}