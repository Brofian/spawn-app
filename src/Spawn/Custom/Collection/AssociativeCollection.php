<?php declare(strict_types=1);

namespace spawnCore\Custom\Collection;

class AssociativeCollection extends AbstractCollectionBase
{

    protected array $collection = array();
    protected array $keys = array();
    protected int $position = 0;

    /*
     *
     * Custom Functions
     *
     */

    public function set($key, $value)
    {
        $isNewEntry = (false == isset($this->collection[$key]));
        $this->collection[$key] = $value;

        if ($isNewEntry) {
            $this->generateOrUpdateKeys();
        }
    }

    protected function generateOrUpdateKeys()
    {
        $this->keys = [];
        foreach ($this->collection as $key => $item) {
            $this->keys[] = $key;
        }
    }

    public function first()
    {
        if ($this->count() == 0) {
            return null;
        } else {
            return $this->get(0);
        }
    }

    /**
     * @param string|int $key
     * @param mixed $default
     * @return mixed|null
     */
    public function get($key, $default = null)
    {
        if (isset($this->collection[$key])) {
            return $this->collection[$key];
        }

        if (isset($this->keys[$key])) {
            return $this->collection[$this->keys[$key]];
        }

        return $default;
    }

    /**
     * @param string|int $key
     * @return bool
     */
    public function has($key)
    {
        return isset($this->collection[$key]) || isset($this->keys[$key]);
    }

    public function last()
    {
        $count = $this->count();

        if ($count == 0) {
            return null;
        } else {
            return $this->get($count - 1);
        }
    }

    public function sort(callable $sortingMethod)
    {
        uasort($this->collection, $sortingMethod);
        $this->generateOrUpdateKeys();
    }

    public function filter(callable $filterMethod)
    {
        $this->collection = array_filter($this->collection, $filterMethod);
        $this->generateOrUpdateKeys();
    }

    public function getArray(): array
    {
        return $this->collection;
    }

    protected function getByIndex(int $index)
    {
        return $this->collection[$this->keys[$index]];
    }

    protected function getCurrentKey()
    {
        if (isset($this->keys[$this->position])) {
            return $this->keys[$this->position];
        }

        return count($this->keys);
    }
}