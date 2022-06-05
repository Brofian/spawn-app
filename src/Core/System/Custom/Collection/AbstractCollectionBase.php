<?php declare(strict_types=1);

namespace SpawnCore\System\Custom\Collection;


use Countable;
use Iterator;

abstract class AbstractCollectionBase implements Iterator, Countable
{

    protected array $collection = array();
    protected int $position;
    protected ?int $count = null;
    
    public function current(): mixed
    {
        return $this->getByIndex($this->position);
    }

    abstract protected function getByIndex(int $index);

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

    abstract protected function getCurrentKey();

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
        return $this->count ?? ($this->count = count($this->collection));
    }

}