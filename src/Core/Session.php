<?php
/**
 * Session Handler
 * 
 * Secure session management with CSRF protection
 */

namespace App\Core;

class Session
{
    private static bool $started = false;
    private array $config;

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * Start the session with secure settings
     */
    public function start(): void
    {
        if (self::$started || session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        // Determine if connection is secure
        $isSecure = $this->isSecureConnection();

        // Configure session settings
        ini_set('session.cookie_httponly', 1);
        ini_set('session.cookie_secure', $isSecure ? 1 : 0);
        ini_set('session.use_strict_mode', 1);
        ini_set('session.use_only_cookies', 1);
        ini_set('session.cookie_samesite', 'Lax');
        ini_set('session.gc_maxlifetime', $this->config['lifetime'] ?? 7200);

        if (!empty($this->config['name'])) {
            session_name($this->config['name']);
        }

        session_start();
        self::$started = true;

        // Regenerate session ID periodically to prevent fixation
        $this->regenerateIfNeeded();
    }

    /**
     * Check if connection is HTTPS
     */
    private function isSecureConnection(): bool
    {
        return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
            || (!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);
    }

    /**
     * Regenerate session ID if needed
     */
    private function regenerateIfNeeded(): void
    {
        $regenerationInterval = 1800; // 30 minutes
        
        if (!isset($_SESSION['_last_regeneration'])) {
            $_SESSION['_last_regeneration'] = time();
        } elseif (time() - $_SESSION['_last_regeneration'] > $regenerationInterval) {
            $this->regenerate();
        }
    }

    /**
     * Regenerate session (use after login)
     */
    public function regenerate(): void
    {
        $oldData = $_SESSION;
        session_regenerate_id(true);
        $_SESSION = $oldData;
        $_SESSION['_last_regeneration'] = time();
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    /**
     * Get a session value
     */
    public function get(string $key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Set a session value
     */
    public function set(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Check if session has a key
     */
    public function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    /**
     * Remove a session value
     */
    public function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    /**
     * Get CSRF token (generate if not exists)
     */
    public function getCsrfToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Validate CSRF token
     */
    public function validateCsrfToken(?string $token): bool
    {
        if (empty($token) || empty($_SESSION['csrf_token'])) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Set flash message
     */
    public function flash(string $type, string $message): void
    {
        $_SESSION['_flash'] = ['type' => $type, 'message' => $message];
    }

    /**
     * Get and clear flash message
     */
    public function getFlash(): ?array
    {
        $flash = $_SESSION['_flash'] ?? null;
        unset($_SESSION['_flash']);
        return $flash;
    }

    /**
     * Destroy the session
     */
    public function destroy(): void
    {
        $_SESSION = [];
        
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
        
        session_destroy();
        self::$started = false;
    }
}
