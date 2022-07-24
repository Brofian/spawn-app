<?php

namespace SpawnCore\System\Database\Criteria\Orders;

class OrderBy {

    public const ORDER_ASC = 'ASC';
    public const ORDER_DESC = 'DESC';

    protected string $column;
    protected string $direction;

    public function __construct(string $column, string $direction = self::ORDER_ASC)
    {
        $this->column = $column;
        $this->direction = (in_array($direction, [self::ORDER_ASC, self::ORDER_DESC])) ? $direction : self::ORDER_ASC;
    }

    public function getColumn(): string
    {
        return $this->column;
    }

    public function setColumn(string $column): self
    {
        $this->column = $column;
        return $this;
    }

    public function getDirection(): string
    {
        return $this->direction;
    }

    public function setDirection(string $direction): self
    {
        $this->direction = $direction;
        return $this;
    }

}