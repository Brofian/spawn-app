<?php declare(strict_types=1);

namespace spawnCore\Custom\Gadgets;


use spawnCore\Custom\FoundationStorage\Mutable;

class ValueBag extends Mutable
{

    public function toArray()
    {
        return get_object_vars($this);
    }

}