<?php

namespace spawnApp\Extensions\Twig;

use spawn\system\Core\Custom\CSRFTokenAssistant;
use spawn\system\Core\Extensions\Twig\Abstracts\FunctionExtension;
use spawn\system\Core\Services\ServiceContainerProvider;

class FormHoneypotFunction extends FunctionExtension {


    protected function getFunctionName(): string
    {
        return 'honeypot';
    }

    protected function getFunctionFunction(): callable
    {
        return function(string $fakePurpose) {
            return '<input type="text" class="d-none" name="'.$fakePurpose.'" />';
        };
    }

    protected function getFunctionOptions(): array
    {
        return [
            'is_safe' => ['html']
        ];
    }
}