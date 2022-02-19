<?php

namespace SpawnCore\Defaults\Events;

use SpawnCore\System\Custom\Gadgets\ScssHelper;
use SpawnCore\System\EventSystem\Event;

class ScssCompileEvent extends Event {

    protected ScssHelper $scssHelper;

    public function __construct(
        ScssHelper $scssHelper
    )
    {
        $this->scssHelper = $scssHelper;
    }

    public function getScssHelper(): ScssHelper {
        return $this->scssHelper;
    }

}