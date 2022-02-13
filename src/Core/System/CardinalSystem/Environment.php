<?php declare(strict_types=1);

namespace SpawnCore\System\CardinalSystem;

use SpawnCore\System\CardinalSystem\Guardian\Guardian;
use SpawnCore\System\Custom\Gadgets\Logger;
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