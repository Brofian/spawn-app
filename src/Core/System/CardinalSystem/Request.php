<?php declare(strict_types=1);

namespace SpawnCore\System\CardinalSystem;

/*
 *  The default Class to store all Request Information
 */


use Doctrine\DBAL\Exception;
use JsonException;
use SpawnBackend\Controller\Backend\AdminLoginController;
use SpawnBackend\Database\AdministratorTable\AdministratorEntity;
use SpawnCore\Defaults\Database\SeoUrlTable\SeoUrlEntity;
use SpawnCore\Defaults\Database\UserTable\UserEntity;
use SpawnCore\Defaults\Services\ConfigurationManager;
use SpawnCore\System\Custom\Collection\AssociativeCollection;
use SpawnCore\System\Custom\FoundationStorage\Mutable;
use SpawnCore\System\Custom\Gadgets\JsonHelper;
use SpawnCore\System\Custom\Gadgets\Logger;
use SpawnCore\System\Custom\Response\Exceptions\JsonConvertionException;
use SpawnCore\System\Custom\Throwables\DatabaseConnectionException;
use SpawnCore\System\Database\Entity\RepositoryException;
use SpawnCore\System\NavigationSystem\Navigator;
use SpawnCore\System\ServiceSystem\ServiceContainerProvider;

class Request extends Mutable
{
    protected AssociativeCollection $get;
    protected AssociativeCollection $post;
    protected AssociativeCollection $cookies;
    protected array $curl_values = [];

    protected string $requestURI;
    protected string $requestHostName;
    protected string $requestPath;
    protected string $requestMethod;
    protected string $clientIp;
    protected bool $isHttps;
    protected ?UserEntity $user = null;
    protected ?AdministratorEntity $administrator = null;

    protected SeoUrlEntity $seoUrl;

    /**
     * @throws DatabaseConnectionException
     * @throws RepositoryException
     */
    public function __construct()
    {
        $this->enrichGetValueBag();
        $this->enrichPostValueBag();
        $this->enrichCookieValueBag();

        $this->enrichIsHttps();
        $this->enrichRequestHostName();
        $this->enrichRequestPath();
        $this->enrichRequestURI();
        $this->enrichRequestMethod();
        $this->enrichClientIp();

        $this->checkForRewriteUrl();

        if (MODE === 'dev') {
            $this->writeAccessLogEntry();
        }
    }

    public function getVars(): array {
        return [
            'cookies' => $this->getCookies()->getArray(),
            'get' => $this->getGet()->getArray(),
            'post' => $this->getPost()->getArray(),
            'curl' => $this->getCurlValues(),
            'uri' => $this->getRequestURI(),
            'seoUrl' => $this->getSeoUrl() ? $this->getSeoUrl()->getName() : ''
        ];
    }

    protected function enrichGetValueBag(): void
    {
        $this->get = new AssociativeCollection();

        // If the $_GET variable is set, use it directly.
        // But if the server redirect removes the get parameters, use the request URI instead
        if(!empty($_GET)) {
            foreach ($_GET as $key => $value) {
                $this->get->set($key, $value);
            }
        }
        else {
            $requestUri = $_SERVER['REQUEST_URI'];
            $getParameterStart = strpos($requestUri, '?');
            if($getParameterStart !== false) {
                $parameterString = substr($requestUri, $getParameterStart+1);
                parse_str($parameterString, $getParameters);
                foreach ($getParameters as $key => $value) {
                    $this->get->set($key, $value);
                }
            }
        }
    }

    /**
     * @throws JsonConvertionException
     * @throws JsonException
     */
    protected function enrichPostValueBag(): void
    {
        $this->post = new AssociativeCollection();

        if(!empty($_POST)) {
            foreach ($_POST as $key => $value) {
                $this->post->set($key, $value);
            }
        }
        else {
            $bodyData = file_get_contents('php://input');
            if(JsonHelper::validateJson($bodyData)) {
                foreach(JsonHelper::jsonToArray($bodyData) as $key => $value) {
                    $this->post->set($key, $value);
                }
            }
        }
    }

    protected function enrichCookieValueBag(): void
    {
        $this->cookies = new AssociativeCollection();
        foreach ($_COOKIE as $key => $value) {
            $this->cookies->set($key, $value);
        }
    }

    protected function enrichIsHttps(): void
    {
        $serverHttps = $_SERVER['HTTPS'] ?? '';
        $this->isHttps = ('on' === $serverHttps);
    }

    protected function enrichRequestHostName(): void
    {
        $this->requestHostName = $_SERVER['HTTP_HOST'] ?? "";
    }

    protected function enrichRequestPath(): void
    {
        //remove getParameter
        $requestUri = $_SERVER['REQUEST_URI'];
        $getParameterStart = strpos($requestUri, '?');
        if($getParameterStart !== false) {
            $requestUri = substr($requestUri, 0, $getParameterStart);
        }

        //set requestPath
        $this->requestPath = $requestUri ?? '/';
    }

    protected function enrichRequestUri(): void
    {
        $https = $this->isHttps ? 'https' : 'http';
        $hostname = $this->requestHostName;
        $path = $this->requestPath;

        $this->requestURI = "{$https}://{$hostname}{$path}";
    }

    protected function enrichRequestMethod(): void
    {
        $this->requestMethod = $_SERVER['REQUEST_METHOD'];
    }


    /**
     * @throws DatabaseConnectionException
     * @throws RepositoryException
     */
    protected function checkForRewriteUrl(): void
    {
        /** @var Navigator $routingHelper */
        $routingHelper = ServiceContainerProvider::getServiceContainer()->getServiceInstance('system.routing.helper');

        $this->seoUrl = $routingHelper->rewriteURL(
            $this->requestPath,
            $this->curl_values
        );
    }

    public function checkAccessPermissionStatus(): void {
        /** @var Navigator $routingHelper */
        $routingHelper = ServiceContainerProvider::getServiceContainer()->getServiceInstance('system.routing.helper');

        if($this->seoUrl->isRequiresAdmin() && !$this->getAdministrator()) {
            // route to backend login page
            $this->seoUrl = $routingHelper->route(AdminLoginController::ADMIN_LOGIN_ROUTE);
        }
        elseif($this->seoUrl->isRequiresUser() && !$this->getUser()) {
            // route to configured frontend login page
            /** @var ConfigurationManager $configurationManager */
            $configurationManager = ServiceContainerProvider::getServiceContainer()->getServiceInstance('system.service.configuration_manager');
            $configuredEntity = $configurationManager->getConfiguration('config_system_user_login_route');
            if($configuredEntity) {
                $this->seoUrl = $routingHelper->getSeoEntityById($configuredEntity);
            }
        }
    }

    public function enrichClientIp(): void
    {
        $remoteAddr = $_SERVER['REMOTE_ADDR'];
        if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] && in_array($remoteAddr, TRUSTED_PROXIES, true)) {
            $remoteAddr = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }

        $this->clientIp = filter_var($remoteAddr, FILTER_VALIDATE_IP) ?? '0.0.0.0';
    }

    public function writeAccessLogEntry(): void
    {
        Logger::writeToAccessLog("Call to \"{$this->requestURI}\"");
    }

    public function getGet(): AssociativeCollection
    {
        return $this->get;
    }

    public function getCookies(): AssociativeCollection
    {
        return $this->cookies;
    }

    public function getPost(): AssociativeCollection
    {
        return $this->post;
    }

    public function getCurlValues(): array
    {
        return $this->curl_values;
    }

    public function getRequestURI(): string
    {
        return $this->requestURI;
    }

    public function getSeoUrl(): SeoUrlEntity {
        return $this->seoUrl;
    }

    public function getClientIp(): string {
        return $this->clientIp;
    }

    public function getUser(): ?UserEntity
    {
        return $this->user;
    }

    public function setUser(?UserEntity $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getAdministrator(): ?AdministratorEntity
    {
        return $this->administrator;
    }

    public function setAdministrator(?AdministratorEntity $administrator): self
    {
        $this->administrator = $administrator;
        return $this;
    }



}
