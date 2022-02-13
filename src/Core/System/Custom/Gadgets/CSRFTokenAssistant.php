<?php

namespace SpawnCore\System\Custom\Gadgets;

use SpawnCore\System\Custom\Throwables\InvalidCsrfTokenException;

class CSRFTokenAssistant
{

    protected const TOKEN_ROOT = 'SECURE_CSRF_TOKEN';
    protected const TOKEN_LIFETIME = 1800 * 1000000; //30min as microseconds

    protected SessionHelper $sessionHelper;

    public function __construct(
        SessionHelper $sessionHelper
    )
    {
        $this->sessionHelper = $sessionHelper;
    }

    public function createToken(string $purpose): string
    {
        $microtime = microtime(true);
        $token = $this->generateToken($purpose . $microtime);

        $tokens = $this->sessionHelper->get(self::TOKEN_ROOT, []);
        $tokens[$purpose][$token] = $microtime;
        $this->sessionHelper->set(self::TOKEN_ROOT, $tokens);

        return $token;
    }

    protected function generateToken(string $string): string
    {
        $tokenString = self::TOKEN_ROOT . '-' . $string;
        return md5($tokenString);
    }

    /**
     * @param string $token
     * @param string $purpose
     * @return bool
     * @throws InvalidCsrfTokenException
     */
    public function validateToken($token, string $purpose): bool
    {
        //is token set?
        if (!is_string($token) || !$token) {
            throw new InvalidCsrfTokenException($purpose);
        }

        //load session tokens
        $tokens = $this->sessionHelper->get(self::TOKEN_ROOT, []);

        //is token set?
        if (!isset($tokens[$purpose][$token])) {
            throw new InvalidCsrfTokenException($purpose);
        }

        //is token lifetime expired?
        $tokenLifetime = microtime(true) - $tokens[$purpose][$token];
        if ($tokenLifetime > self::TOKEN_LIFETIME) {
            unset($tokens[$purpose][$token]);
            throw new InvalidCsrfTokenException($purpose);
        }

        //token ist valid: remove token from list and return true
        unset($tokens[$purpose][$token]);
        $this->sessionHelper->set(self::TOKEN_ROOT, $tokens);
        return true;
    }


}