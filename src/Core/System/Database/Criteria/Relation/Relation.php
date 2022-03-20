<?php declare(strict_types=1);

namespace SpawnCore\System\Database\Criteria\Relation;

use Doctrine\DBAL\Query\QueryBuilder;


class Relation {

    public const TYPE_INNER_JOIN = 'innerJoin';
    public const TYPE_LEFT_JOIN = 'leftJoin';
    public const TYPE_RIGHT_JOIN = 'rightJoin';
    public const TYPE_JOIN = 'join';

    protected string $type;
    protected string $fromTable;
    protected string $fromColumn;
    protected string $toTable;
    protected string $toColumn;


    public function __construct(string $fromTable, string $fromColumn, string $toTable, string $toColumn, string $type = self::TYPE_LEFT_JOIN)
    {
        $this->type = $type;
        $this->fromTable = $fromTable;
        $this->fromColumn = $fromColumn;
        $this->toTable = $toTable;
        $this->toColumn = $toColumn;
    }

    public function applyRelationToQuery(QueryBuilder $query): void {
        $condition = sprintf('(%s.%s = %s.%s)',
            $this->fromTable,
            $this->fromColumn,
            $this->toTable,
            $this->toColumn
        );
        $query->{$this->type}($this->fromTable, $this->toTable, $this->toTable, $condition);
    }

}