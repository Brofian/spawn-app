<?php

namespace SpawnCore\System\Database\Criteria;

use Doctrine\DBAL\Query\QueryBuilder;
use SpawnCore\System\Database\Criteria\Filters\AbstractFilter;
use SpawnCore\System\Database\Criteria\Filters\AndFilter;
use SpawnCore\System\Database\Criteria\Relation\Relation;

class Criteria {

    /** @var AbstractFilter[]  */
    protected array $filters = [];

    /** @var Relation[]  */
    protected array $relations = [];

    public function __construct(AbstractFilter ...$filters)
    {
        foreach($filters as $filter) {
            $this->filters[] = $filter;
        }
    }

    /**
     * @return AbstractFilter[]
     */
    public function getFilters(): array {
        return $this->filters;
    }

    public function addFilter(AbstractFilter $filter): void {
        $this->filters[] = $filter;
    }

    /**
     * @return Relation[]
     */
    public function getRelations(): array {
        return $this->relations;
    }

    public function addRelation(Relation $relation): void {
        $this->relations[] = $relation;
    }



    public function getParameters(): array {
        $params = [];
        foreach($this->filters as $filter) {
            $params = array_merge($params, $filter->getParameters());
        }

        return $params;
    }

    public function generateCriteria(): string {
        if(!empty($this->filters)) {
            return (new AndFilter(...$this->filters))->getCondition();
        }
        return '(1)';
    }

    public function computeRelations(QueryBuilder $queryBuilder): void {
        foreach($this->relations as $relation) {
            $relation->applyRelationToQuery($queryBuilder);
        }
    }

}