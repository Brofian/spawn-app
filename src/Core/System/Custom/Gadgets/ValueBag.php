<?php declare(strict_types=1);

namespace SpawnCore\System\Custom\Gadgets;


use SpawnCore\System\Custom\FoundationStorage\Mutable;

class ValueBag extends Mutable
{

    public function toArray()
    {
        return get_object_vars($this);
    }

}