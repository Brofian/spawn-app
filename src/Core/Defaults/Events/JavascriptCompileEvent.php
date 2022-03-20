<?php

namespace SpawnCore\Defaults\Events;

use SpawnCore\System\Custom\Gadgets\JavascriptHelper;
use SpawnCore\System\EventSystem\Event;

class JavascriptCompileEvent extends Event {

    protected JavascriptHelper $javascriptHelper;

    public function __construct(
        JavascriptHelper $javascriptHelper
    )
    {
        $this->javascriptHelper = $javascriptHelper;
    }

    public function getJavascriptHelper(): JavascriptHelper {
        return $this->javascriptHelper;
    }

}