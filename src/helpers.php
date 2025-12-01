<?php
/**
 * Helper functions for GYMBRO
 * All functions are wrapped to avoid redeclaration if bootstrap.php is also loaded
 */

// Database connection helper
if (!function_exists('getDb')) {
    function getDb() {
        static $pdo = null;
        
        if ($pdo === null) {
            $host = 'db';
            $dbname = 'db';
            $username = 'db';
            $password = 'db';
            
            try {
                $pdo = new PDO(
                    "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
                    $username,
                    $password,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                    ]
                );
            } catch (PDOException $e) {
                die('Database connection failed: ' . $e->getMessage());
            }
        }
        
        return $pdo;
    }
}

// Session management
if (!function_exists('startSession')) {
    function startSession() {
        if (session_status() === PHP_SESSION_NONE) {
            $isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') 
                        || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
                        || (!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);
            
            ini_set('session.cookie_httponly', 1);
            ini_set('session.cookie_secure', $isSecure ? 1 : 0);
            ini_set('session.use_strict_mode', 1);
            ini_set('session.use_only_cookies', 1);
            ini_set('session.cookie_samesite', 'Lax');
            ini_set('session.gc_maxlifetime', 3600);
            session_start();
        }
    }
}

// CSRF token generation and validation
if (!function_exists('generateCsrfToken')) {
    function generateCsrfToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}

if (!function_exists('validateCsrfToken')) {
    function validateCsrfToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
}

if (!function_exists('rotateCsrfToken')) {
    function rotateCsrfToken() {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        return $_SESSION['csrf_token'];
    }
}

// Regenerate session on authentication
if (!function_exists('regenerateSessionOnAuth')) {
    function regenerateSessionOnAuth() {
        $oldSessionData = $_SESSION;
        session_regenerate_id(true);
        
        if (isset($oldSessionData['flash'])) {
            $_SESSION['flash'] = $oldSessionData['flash'];
        }
        
        $_SESSION['last_regeneration'] = time();
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
}

// Authentication helpers
if (!function_exists('isLoggedIn')) {
    function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
}

if (!function_exists('requireLogin')) {
    function requireLogin() {
        if (!isLoggedIn()) {
            redirect('/login');
            exit;
        }
    }
}

if (!function_exists('getCurrentUserId')) {
    function getCurrentUserId() {
        return $_SESSION['user_id'] ?? null;
    }
}

if (!function_exists('getCurrentUser')) {
    function getCurrentUser() {
        $userId = getCurrentUserId();
        if (!$userId) {
            return null;
        }
        
        $db = getDb();
        $stmt = $db->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->execute([$userId]);
        return $stmt->fetch();
    }
}

// Routing helpers
if (!function_exists('redirect')) {
    function redirect($path) {
        header("Location: $path");
        exit;
    }
}

if (!function_exists('url')) {
    function url($path) {
        return $path;
    }
}

// Validation helpers
if (!function_exists('validateEmail')) {
    function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}

if (!function_exists('validatePassword')) {
    function validatePassword($password) {
        return preg_match('/^(?=.*[A-Z])(?=(?:.*\d){2,})(?=(?:.*[@#!?]){2,}).{8,}$/', $password);
    }
}

if (!function_exists('validateAge')) {
    function validateAge($age) {
        return is_numeric($age) && $age >= 13 && $age <= 100;
    }
}

if (!function_exists('validateWorkoutStyles')) {
    function validateWorkoutStyles($styles) {
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
}

// Flash messages
if (!function_exists('setFlash')) {
    function setFlash($type, $message) {
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    }
}

if (!function_exists('getFlash')) {
    function getFlash() {
        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $flash;
        }
        return null;
    }
}

// Sanitization
if (!function_exists('escape')) {
    function escape($string) {
        return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
    }
}

// JSON helpers
if (!function_exists('jsonResponse')) {
    function jsonResponse($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
