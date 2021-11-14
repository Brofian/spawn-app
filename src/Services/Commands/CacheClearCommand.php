<?php

namespace spawnApp\Services\Commands;

use bin\spawn\IO;
use spawn\system\Core\Custom\AbstractCommand;

class CacheClearCommand extends AbstractCommand  {

    public static function getCommand(): string {
        return 'cache:clear';
    }

    public static function getShortDescription(): string    {
        return 'Clears the caches. If no parameter is passed, all caches are cleared';
    }

    protected static function getParameters(): array   {
        return [
            'all' => ['all', 'a'],
            'resources' => 'r',
            'compiled' => 'c',
            'generated' => 'g',
            'twig' => 't',
        ];
    }

    public function execute(array $parameters): int  {
        $clearAll = !!$parameters['all'];

        IO::printWarning('Start clearing caches...');
        $startingTime = microtime(true) * 1000;

        if($clearAll || $parameters['resources']) {
            $this->clearResourceCache();
        }
        if($clearAll || $parameters['compiled']) {
            $this->clearCompiledCache();
        }
        if($clearAll || $parameters['generated']) {
            $this->clearGeneratedCache();
        }
        if($clearAll || $parameters['twig']) {
            $this->clearTwigCache();
        }

        $endingTime = microtime(true) * 1000;
        $duration = ($endingTime-$startingTime);
        IO::printWarning('Cleared caches in '.round($duration, 3).'ms!');

        return 0;
    }



    protected function clearCompiledCache(): void {
        $cacheDir = ROOT.CACHE_DIR.'/public';
        rrmdir($cacheDir);
    }

    protected function clearGeneratedCache(): void {
        $cacheDir = ROOT.CACHE_DIR.'/private/generated';
        rrmdir($cacheDir);
    }

    protected function clearResourceCache(): void {
        $cacheDir = ROOT.CACHE_DIR.'/resources';
        rrmdir($cacheDir);
    }

    protected function clearTwigCache(): void {
        $cacheDir = ROOT.CACHE_DIR.'/private/twig';
        rrmdir($cacheDir);
    }



}