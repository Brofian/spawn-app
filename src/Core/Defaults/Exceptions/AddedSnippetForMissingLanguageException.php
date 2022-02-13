<?php

namespace SpawnCore\Defaults\Exceptions;

use SpawnCore\System\Custom\Throwables\AbstractException;
use Throwable;

class AddedSnippetForMissingLanguageException extends AbstractException {

    public function __construct(string $missingLanguageShort, Throwable $previous = null)
    {
        parent::__construct([
            'lang' => $missingLanguageShort
        ], $previous);
    }

    protected function getMessageTemplate(): string
    {
        return 'Tried adding snippets to non existent language "%lang%"!';
    }

    protected function getExitCode(): int
    {
        return 142;
    }
}