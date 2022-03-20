<?php declare(strict_types=1);

namespace SpawnCore\System\Custom\Gadgets;

use Exception;
use SpawnCore\System\Custom\Throwables\DatabaseConnectionException;
use SpawnCore\System\Custom\Throwables\HeadersSendByException;
use SpawnCore\System\Database\Entity\RepositoryException;
use SpawnCore\System\ServiceSystem\ServiceContainerProvider;

class HeaderHelper
{

    public const RC_REDIRECT_TEMPORARILY = 307;
    public const RC_REDIRECT_SESSION = 302;
    public const RC_REDIRECT_FINAL = 301;


    /**
     * @param string $targetId
     * @param array $parameters
     * @param int $responseCode
     * @param bool $replaceExisting
     * @throws HeadersSendByException
     * @throws \Doctrine\DBAL\Exception
     * @throws DatabaseConnectionException
     * @throws RepositoryException
     */
    public function redirect(string $targetId, array $parameters = [], bool $replaceExisting = false): void
    {
        $routingHelper = ServiceContainerProvider::getServiceContainer()->getServiceInstance('system.routing.helper');
        $location = $routingHelper->getLinkFromId($targetId, $parameters);
        $this->setHeader("Location: " . $location, $replaceExisting);
    }


    /**
     * @throws HeadersSendByException
     */
    public function setHeader(string $header, bool $replaceExisting = false): void
    {
        try {
            header($header, $replaceExisting);
        } catch (Exception $exception) {
            throw new HeadersSendByException();
        }
    }


}