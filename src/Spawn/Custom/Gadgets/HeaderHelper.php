<?php declare(strict_types=1);

namespace spawnCore\Custom\Gadgets;

use Exception;
use spawnCore\Custom\Throwables\HeadersSendByException;
use spawnCore\ServiceSystem\ServiceContainerProvider;

class HeaderHelper
{

    public const RC_REDIRECT_TEMPORARILY = 307;
    public const RC_REDIRECT_SESSION = 302;
    public const RC_REDIRECT_FINAL = 301;

    private bool $headersSendBy = false;


    /**
     * @param string $targetId
     * @param array $parameters
     * @param int $responseCode
     * @param bool $replaceExisting
     * @throws HeadersSendByException
     */
    public function redirect(string $targetId, array $parameters = [], int $responseCode = self::RC_REDIRECT_TEMPORARILY, bool $replaceExisting = false)
    {
        $routingHelper = ServiceContainerProvider::getServiceContainer()->getServiceInstance('system.routing.helper');
        $location = $routingHelper->getLinkFromId($targetId, $parameters);
        $this->setHeader("Location: " . $location, $responseCode, $replaceExisting);
    }


    /**
     * @param string $header
     * @param int $responseCode
     * @param bool $replaceExisting
     * @throws HeadersSendByException
     */
    public function setHeader(string $header, int $responseCode = 200, bool $replaceExisting = false)
    {
        try {
            header($header, $replaceExisting, $responseCode);
        } catch (Exception $exception) {
            throw new HeadersSendByException();
        }
    }


}