<?php declare(strict_types=1);

namespace SpawnCore\System\Custom\Collection;

use OutOfBoundsException;

class Collection extends AbstractCollectionBase
{

    protected array $collection = [];
    protected int $position = 0;

    public function add($value): void
    {
        $this->collection[] = $value;
    }

    public function overwrite(array $collection): void
    {
        $this->collection = $collection;
    }


    /*
     *
     * Custom Functions
     *
     */

    public function set(int $key, $value): void
    {
        if (isset($this->collection[$key])) {
            $this->collection[$key] = $value;
        } else {
            $this->collection[] = $value;
        }
    }

    public function sort(callable $sortingMethod): void
    {
        uasort($this->collection, $sortingMethod);
    }

    public function filter(callable $filterMethod): void
    {
        $this->collection = array_filter($this->collection, $filterMethod);
    }

    public function first()
    {
        return $this->get(0);
    }

    public function get(int $key)
    {
        return $this->collection[$key] ?? null;
    }

    public function last()
    {
        $count = $this->count();
        return $this->get($count - 1);
    }

    public function getArray(): array
    {
        return $this->collection;
    }

    public function getArrayRange(int $limit, int $offset = 0): array {
        if($limit < 1 || $offset < 0 || $offset >= $this->count()) {
            // out of bound
            throw new OutOfBoundsException('The given range is out of the allowed bounds');
        }

        $range = [];
        $max = min($offset+$limit, $this->count());
        for($i = $offset; $i < $max; $i++) {
            $range[] = $this->collection[$i];
        }

        return $range;
    }

    protected function getByIndex(int $index)
    {
        return $this->collection[$index];
    }

    protected function getCurrentKey()
    {
        return $this->position;
    }


}