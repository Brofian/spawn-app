<?php declare(strict_types = 1);
namespace SpawnCore\System\Database\Criteria\Filters;

abstract class AbstractFilter {

    abstract public function getCondition(): string;
    abstract public function getParameters(): array;

    public static function getFilterFromType(string $type, string $column = null, $value = null): AbstractFilter {
        switch($type) {
            case 'LIKE':
                if($column === null) {
                    throw new \RuntimeException('LIKE Filters require a column for comparison!');
                }
                if(!is_string($value)) {
                    throw new InvalidFilterValueException(get_debug_type($value), LikeFilter::class);
                }
                return new LikeFilter($column, $value);
            case 'MULT':
                if($column === null) {
                    throw new \RuntimeException('LIKE Filters require a column for comparison!');
                }
                if(!is_array($value)) {
                    throw new InvalidFilterValueException(get_debug_type($value), InFilter::class);
                }
                return new InFilter($column, $value);
            case 'RANGE':
                if($column === null) {
                    throw new \RuntimeException('LIKE Filters require a column for comparison!');
                }
                if(!is_array($value)) {
                    throw new InvalidFilterValueException(get_debug_type($value), BetweenFilter::class);
                }
                return new BetweenFilter($column, $value[0], $value[1]);
            case 'NULL':
                if($value !== null) {
                    throw new \RuntimeException('NULL Filters cannot have a value associated. Pass NULL instead or remove value!');
                }
                return new IsNullFilter($column);
            case 'NOT':
                if($column !== null) {
                    throw new \RuntimeException('NOT Filters cannot have a column associated. Pass NULL instead!');
                }
                return new NotFilter(
                    self::getFilterFromType($value)
                );
            case 'AND':
                if($column !== null) {
                    throw new \RuntimeException('AND Filters cannot have a column associated. Pass NULL instead!');
                }
                return new AndFilter(
                    ...array_map(self::class.'::getFilterFromType', $value)
                );
            case 'OR':
                if($column !== null) {
                    throw new \RuntimeException('OR Filters cannot have a column associated. Pass NULL instead!');
                }
                return new OrFilter(
                    ...array_map(self::class.'::getFilterFromType', $value)
                );
            case '<':
                if($column === null) {
                    throw new \RuntimeException('Less than Filters require a column for comparison!');
                }
                if(!is_numeric($value)) {
                    throw new InvalidFilterValueException(get_debug_type($value), LessThanFilter::class);
                }
                return new LessThanFilter($column, $value);
            case '>':
                if($column === null) {
                    throw new \RuntimeException('Greater than Filters require a column for comparison!');
                }
                if(!is_numeric($value)) {
                    throw new InvalidFilterValueException(get_debug_type($value), GreaterThanFilter::class);
                }
                return new GreaterThanFilter($column, $value);
            case '<=':
                if($column === null) {
                    throw new \RuntimeException('Less than or equal Filters require a column for comparison!');
                }
                if(!is_numeric($value)) {
                    throw new InvalidFilterValueException(get_debug_type($value), LessThanOrEqualsFilter::class);
                }
                return new LessThanOrEqualsFilter($column, $value);
            case '>=':
                if($column === null) {
                    throw new \RuntimeException('Greater than or equal Filters require a column for comparison!');
                }
                if(!is_numeric($value)) {
                    throw new InvalidFilterValueException(get_debug_type($value), GreaterThanOrEqualsFilter::class);
                }
                return new GreaterThanOrEqualsFilter($column, $value);
            case '==':
            default:
                if($column === null) {
                    throw new \RuntimeException('Equal Filters require a column for comparison!');
                }
                return new EqualsFilter($column, $value);
        }
    }
}