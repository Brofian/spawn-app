<?php declare(strict_types=1);

namespace spawnCore\CardinalSystem;

use Doctrine\DBAL\Configuration;
use spawnCore\CardinalSystem\Guardian\Guardian;
use spawnCore\Custom\Gadgets\Logger;
use Throwable;

/*
 * The Main Environment to handle the system
 */

class Environment
{

    protected Kernel $kernel;


    public function __construct()
    {
        $this->kernel = new Kernel();
    }

    public function handle()
    {
        try {
            $this->kernel->handle();
            return $this->kernel->getAnswer();

        } catch (Throwable $exception) {
            $guardian = new Guardian();
            $guardian->handleException($exception);
            return $guardian->getHandleResponse();
        }
    }

}