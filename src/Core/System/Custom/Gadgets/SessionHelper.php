<?php declare(strict_types=1);

namespace SpawnCore\System\Custom\Gadgets;

class SessionHelper
{
    private array $session = array();

    public function __construct()
    {
        $this->startSession();
        $this->session = $_SESSION;
    }

    private function startSession(): bool
    {
        if ($this->isSessionActive() == false) {
            return session_start();
        }
        return false;
    }

    private function isSessionActive(): bool
    {
        return (session_status() == PHP_SESSION_ACTIVE);
    }

    public function __destruct()
    {
        if ($this->isSessionActive()) {
            session_write_close();
        }
    }

    public function set(string $key, $value, bool $overrideExisting = true): bool
    {
        if (isset($this->session[$key]) && $overrideExisting == false) {
            return false;
        }

        $_SESSION[$key] = $value;
        $this->session[$key] = $value;
        return true;
    }

    /**
     * @param string $key
     * @param bool $fallback
     * @return mixed
     */
    public function get(string $key, $fallback = null)
    {
        if ($this->isSessionActive() == false || isset($this->session[$key]) == false) {
            return $fallback;
        }
        return $this->session[$key];
    }

    public function destroySession(): bool
    {
        if ($this->isSessionActive()) {
            session_destroy();
            $this->startSession();
            return true;
        }
        return false;
    }

}