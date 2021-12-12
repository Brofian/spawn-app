<?php declare(strict_types=1);

namespace spawnCore\Custom\FoundationStorage;

abstract class Mutable
{

    public function __get($name)
    {
        return $this->get($name);
    }

    public function __set($name, $value)
    {
        $this->set($name, $value, true);
    }

    public function set(string $key, $value, bool $allowOverride = true)
    {
        if ($allowOverride || !isset($this->$key)) {
            $this->$key = $value;
        }
    }

    public function get(string $key)
    {
        if ($this->has($key)) {
            return $this->$key;
        } else {
            return null;
        }
    }

    public function has(string $key): bool
    {
        return isset($this->$key);
    }

    public function __isset($name)
    {
        return $this->has($name);
    }

}