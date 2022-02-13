<?php declare(strict_types=1);

namespace SpawnCore\System\Custom\FoundationStorage;

abstract class BaseModule
{

    /**
     * @return mixed
     */
    public abstract function install();

    /**
     * @return mixed
     */
    public abstract function uninstall();

}