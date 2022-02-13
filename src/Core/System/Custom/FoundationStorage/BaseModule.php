<?php declare(strict_types=1);

namespace SpawnCore\System\Custom\FoundationStorage;

abstract class BaseModule
{

    /**
     * @return mixed
     */
    abstract public function install();

    /**
     * @return mixed
     */
    abstract public function uninstall();

}