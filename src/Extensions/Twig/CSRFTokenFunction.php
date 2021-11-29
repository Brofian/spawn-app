<?php

namespace spawnApp\Extensions\Twig;

use spawn\system\Core\Custom\CSRFTokenAssistant;
use spawn\system\Core\Extensions\Twig\Abstracts\FunctionExtension;
use spawn\system\Core\Services\ServiceContainerProvider;

class CSRFTokenFunction extends FunctionExtension {


    protected function getFunctionName(): string
    {
        return 'csrf';
    }

    protected function getFunctionFunction(): callable
    {
        return function(string $purpose) {
            /** @var CSRFTokenAssistant $tokenAssistant */
            $tokenAssistant = ServiceContainerProvider::getServiceContainer()->getServiceInstance('system.csrf_token.helper');
            $token = $tokenAssistant->createToken($purpose);
            return '<input type="hidden" name="csrf" value="'.$token.'" />';
        };
    }

    protected function getFunctionOptions(): array
    {
        return [
            'is_safe' => ['html']
        ];
    }
}