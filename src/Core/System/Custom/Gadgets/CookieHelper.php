<?php declare(strict_types=1);

namespace SpawnCore\System\Custom\Gadgets;

class CookieHelper
{

    private array $cookies = array();


    public function __construct()
    {
        $this->cookies = $_COOKIE;
    }


    public function set(string $key, string $value, bool $overrideExisting = true, string $path = "/", int $expires = 0, bool $secure = false, bool $httpOnly = false, string $sameSite = "Strict")
    {
        if (isset($this->cookies[$key]) && $overrideExisting == false) return false;

        $options = [
            "expires" => $expires,
            "path" => $path,
            "domain" => $_SERVER["HTTP_HOST"],
            "secure" => $secure,
            "httponly" => $httpOnly,
            "samesite" => $sameSite,
        ];

        setcookie($key, $value, $options);
        $this->cookies[$key] = $value;
        $_COOKIE[$key] = $value;
        return false;
    }


    /**
     * @param string $key
     * @param bool $fallback
     * @return mixed
     */
    public function get(string $key, bool $fallback = null)
    {
        if (isset($this->cookies[$key])) {
            return $this->cookies[$key];
        }
        return $fallback;
    }

    /**
     * @return array
     */
    public function getCookies(): array
    {
        return $this->cookies;
    }
}