<?php declare(strict_types=1);

namespace spawnCore\CardinalSystem;

/*
 *  The default Class to store all Request Information
 */


use spawnCore\Custom\Collection\AssociativeCollection;
use spawnCore\Custom\FoundationStorage\Mutable;
use spawnCore\Custom\Gadgets\Logger;
use spawnCore\NavigationSystem\Navigator;
use spawnCore\ServiceSystem\ServiceContainerProvider;

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
    protected bool $isHttps;


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

        $this->checkForRewriteUrl();

        if (MODE == 'dev') {
            $this->writeAccessLogEntry();
        }
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

    protected function enrichPostValueBag(): void
    {
        $this->post = new AssociativeCollection();
        foreach ($_POST as $key => $value) {
            $this->post->set($key, $value);
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
        $this->isHttps = ('on' == $serverHttps);
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

    protected function enrichRequestUri()
    {
        $https = $this->isHttps ? 'https' : 'http';
        $hostname = $this->requestHostName;
        $path = $this->requestPath;

        $this->requestURI = "{$https}://{$hostname}{$path}";
    }

    protected function enrichRequestMethod()
    {
        $this->requestMethod = $_SERVER['REQUEST_METHOD'];
    }

    protected function checkForRewriteUrl()
    {

        if ($this->get->get('controller') != null && $this->get->get('action') != null) {
            return;
        }

        /** @var Navigator $routingHelper */
        $routingHelper = ServiceContainerProvider::getServiceContainer()->getServiceInstance('system.routing.helper');

        $newURL = $routingHelper->rewriteURL(
            $this->requestPath,
            $this->curl_values
        );

        $parts = parse_url($newURL);
        if (isset($parts['query'])) {
            //read and add get parameters
            parse_str($parts['query'], $query);

            foreach ($query as $key => $value) {
                $this->get->set($key, $value);
            }
        }
    }


    public function writeAccessLogEntry()
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


}
