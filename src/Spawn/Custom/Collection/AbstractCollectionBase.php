<?php declare(strict_types=1);

namespace spawnCore\Custom\Collection;


use Countable;
use Iterator;

abstract class AbstractCollectionBase implements Iterator, Countable
{

    protected array $collection = array();
    protected int $position;


    public function current(): mixed
    {
        return $this->getByIndex($this->position);
    }

    protected abstract function getByIndex(int $index);

    /*
     *
     * Iterator Functions
     *
     */

    public function next(): void
    {
        $this->position++;
    }


    public function key(): mixed
    {
        return $this->getCurrentKey();
    }

    protected abstract function getCurrentKey();

    public function valid(): bool
    {
        return isset($this->collection[$this->getCurrentKey()]);
    }

    public function rewind(): void
    {
        $this->position = 0;
    }


    /*
     *
     * Countable Functions
     *
     */

    public function count(): int
    {
        return count($this->collection);
    }

}