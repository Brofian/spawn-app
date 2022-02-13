<?php declare(strict_types=1);

namespace SpawnCore\System\Custom\Collection;

class Collection extends AbstractCollectionBase
{

    protected array $collection = array();
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

    protected function getByIndex(int $index)
    {
        return $this->collection[$index];
    }

    protected function getCurrentKey()
    {
        return $this->position;
    }


}