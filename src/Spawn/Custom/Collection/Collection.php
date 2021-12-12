<?php declare(strict_types=1);

namespace spawnCore\Custom\Collection;

class Collection extends AbstractCollectionBase
{

    protected array $collection = array();
    protected int $position = 0;

    public function add($value)
    {
        $this->collection[] = $value;
    }

    public function overwrite(array $collection)
    {
        $this->collection = $collection;
    }


    /*
     *
     * Custom Functions
     *
     */

    public function set(int $key, $value)
    {
        if (isset($this->collection[$key])) {
            $this->collection[$key] = $value;
        } else {
            $this->collection[] = $value;
        }
    }

    public function sort(callable $sortingMethod)
    {
        uasort($this->collection, $sortingMethod);
    }

    public function filter(callable $filterMethod)
    {
        $this->collection = array_filter($this->collection, $filterMethod);
    }

    public function first()
    {
        return $this->get(0);
    }

    public function get(int $key)
    {
        if (isset($this->collection[$key])) {
            return $this->collection[$key];
        }

        return null;
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