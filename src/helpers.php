<?php
// Database connection helper
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

// Session management
function startSession() {
    if (session_status() === PHP_SESSION_NONE) {
        // Only set secure cookie if using HTTPS
        $isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') 
                    || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
                    || (!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);
        
        ini_set('session.cookie_httponly', 1);
        ini_set('session.cookie_secure', $isSecure ? 1 : 0);
        ini_set('session.use_strict_mode', 1);
        ini_set('session.use_only_cookies', 1);
        ini_set('session.cookie_samesite', 'Lax');
        ini_set('session.gc_maxlifetime', 3600); // 1 hour
        session_start();
    }
}

// CSRF token generation and validation
function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function rotateCsrfToken() {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf_token'];
}

// Regenerate session on authentication to prevent session fixation
function regenerateSessionOnAuth() {
    // Store any data we need to preserve
    $oldSessionData = $_SESSION;
    
    // Regenerate session ID (destroy old session)
    session_regenerate_id(true);
    
    // Restore flash messages if any (they should persist through login)
    if (isset($oldSessionData['flash'])) {
        $_SESSION['flash'] = $oldSessionData['flash'];
    }
    
    $_SESSION['last_regeneration'] = time();
    
    // Generate new CSRF token for the new session
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Authentication helpers
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        redirect('/login');
        exit;
    }
}

function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

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

// Routing helpers
function redirect($path) {
    header("Location: $path");
    exit;
}

function url($path) {
    return $path;
}

// Validation helpers
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function validatePassword($password) {
    // Min 8 chars, 1 uppercase, 2 digits, 2 symbols from @#!?
    return preg_match('/^(?=.*[A-Z])(?=(?:.*\d){2,})(?=(?:.*[@#!?]){2,}).{8,}$/', $password);
}

function validateAge($age) {
    return is_numeric($age) && $age >= 13 && $age <= 100;
}

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

// Flash messages
function setFlash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

// Sanitization
function escape($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

// JSON helpers
function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
