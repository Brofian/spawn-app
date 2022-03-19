<?php declare(strict_types = 1);
namespace SpawnBackend\Extensions\Twig;


use SpawnCore\System\Custom\RenderExtensions\Twig\Abstracts\FunctionExtension;

class FormHoneypotFunction extends FunctionExtension {


    protected function getFunctionName(): string
    {
        return 'honeypot';
    }

    protected function getFunctionFunction(): callable
    {
        return static function(string $fakePurpose) {
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