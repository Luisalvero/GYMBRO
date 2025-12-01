<?php
/**
 * Application Bootstrap
 * 
 * Initializes the application, loads configuration,
 * sets up error handling, and provides global helper functions
 */

// Set error reporting based on environment
$config = require __DIR__ . '/../config/app.php';

if ($config['app']['debug']) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/../storage/logs/error.log');
}

// Set timezone
date_default_timezone_set($config['app']['timezone']);

// Autoloader for namespaced classes
spl_autoload_register(function ($class) {
    // Convert namespace to file path
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});

// Initialize core services
use App\Core\Database;
use App\Core\Session;

// Store config globally
$GLOBALS['config'] = $config;

/**
 * Get configuration value
 */
function config(string $key, $default = null) {
    $keys = explode('.', $key);
    $value = $GLOBALS['config'];
    
    foreach ($keys as $k) {
        if (!isset($value[$k])) {
            return $default;
        }
        $value = $value[$k];
    }
    
    return $value;
}

/**
 * Get database connection
 */
function getDb(): \PDO {
    static $db = null;
    if ($db === null) {
        $db = Database::getInstance(config('database'))->getConnection();
    }
    return $db;
}

/**
 * Get session instance
 */
function session(): Session {
    static $session = null;
    if ($session === null) {
        $session = new Session(config('session'));
    }
    return $session;
}

/**
 * Start session
 */
function startSession(): void {
    session()->start();
}

/**
 * Generate CSRF token
 */
function generateCsrfToken(): string {
    return session()->getCsrfToken();
}

/**
 * Validate CSRF token
 */
function validateCsrfToken(?string $token): bool {
    return session()->validateCsrfToken($token);
}

/**
 * Check if user is logged in
 */
function isLoggedIn(): bool {
    return session()->has('user_id');
}

/**
 * Require user to be logged in
 */
function requireLogin(): void {
    if (!isLoggedIn()) {
        redirect('/login');
    }
}

/**
 * Get current user ID
 */
function getCurrentUserId(): ?int {
    return session()->get('user_id');
}

/**
 * Get current user data
 */
function getCurrentUser(): ?array {
    $userId = getCurrentUserId();
    if (!$userId) {
        return null;
    }
    
    static $user = null;
    if ($user === null) {
        $stmt = getDb()->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->execute([$userId]);
        $user = $stmt->fetch();
    }
    
    return $user ?: null;
}

/**
 * Redirect to a URL
 */
function redirect(string $path): void {
    header("Location: $path");
    exit;
}

/**
 * Escape HTML output
 */
function escape(?string $string): string {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Set flash message
 */
function setFlash(string $type, string $message): void {
    session()->flash($type, $message);
}

/**
 * Get flash message
 */
function getFlash(): ?array {
    return session()->getFlash();
}

/**
 * Return JSON response
 */
function jsonResponse(array $data, int $statusCode = 200): void {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

/**
 * Validate password against requirements
 */
function validatePassword(string $password): bool {
    return preg_match('/^(?=.*[A-Z])(?=(?:.*\d){2,})(?=(?:.*[@#!?]){2,}).{8,}$/', $password);
}

/**
 * Validate email format
 */
function validateEmail(string $email): bool {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate age
 */
function validateAge($age): bool {
    return is_numeric($age) && $age >= 13 && $age <= 100;
}

/**
 * Validate workout styles
 */
function validateWorkoutStyles($styles): bool {
    $valid = ['calisthenics', 'weightlifting', 'cardio', 'athletic'];
    if (!is_array($styles) || empty($styles)) {
        return false;
    }
    foreach ($styles as $style) {
        if (!in_array($style, $valid)) {
            return false;
        }
    }
    return true;
}

/**
 * Regenerate session on auth (prevents session fixation)
 */
function regenerateSessionOnAuth(): void {
    session()->regenerate();
}

/**
 * Get application URL
 */
function url(string $path = ''): string {
    return config('app.url') . '/' . ltrim($path, '/');
}

/**
 * Check if current environment is production
 */
function isProduction(): bool {
    return config('app.env') === 'production';
}

/**
 * Log an error message
 */
function logError(string $message, array $context = []): void {
    $logMessage = date('[Y-m-d H:i:s] ') . $message;
    if (!empty($context)) {
        $logMessage .= ' ' . json_encode($context);
    }
    error_log($logMessage);
}
