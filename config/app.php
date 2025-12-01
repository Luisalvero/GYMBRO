<?php
/**
 * Application Configuration
 * 
 * This file loads environment variables and provides configuration
 * for different environments (development, production)
 */

// Load environment variables from .env file if it exists
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '#') === 0) continue; // Skip comments
        if (strpos($line, '=') === false) continue;
        
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        
        // Remove quotes if present
        if (preg_match('/^(["\']).*\1$/', $value)) {
            $value = substr($value, 1, -1);
        }
        
        if (!array_key_exists($name, $_ENV)) {
            $_ENV[$name] = $value;
            putenv("$name=$value");
        }
    }
}

/**
 * Get environment variable with fallback
 */
function env(string $key, $default = null) {
    $value = $_ENV[$key] ?? getenv($key);
    if ($value === false) {
        return $default;
    }
    
    // Convert string booleans
    switch (strtolower($value)) {
        case 'true':
        case '(true)':
            return true;
        case 'false':
        case '(false)':
            return false;
        case 'null':
        case '(null)':
            return null;
    }
    
    return $value;
}

/**
 * Application Configuration Array
 */
return [
    // Application
    'app' => [
        'name' => env('APP_NAME', 'GymBro'),
        'url' => env('APP_URL', 'http://localhost'),
        'env' => env('APP_ENV', 'development'),
        'debug' => env('APP_DEBUG', true),
        'timezone' => env('APP_TIMEZONE', 'America/New_York'),
    ],
    
    // Database
    'database' => [
        'host' => env('DB_HOST', 'db'),
        'port' => env('DB_PORT', 3306),
        'name' => env('DB_NAME', 'db'),
        'user' => env('DB_USER', 'db'),
        'password' => env('DB_PASSWORD', 'db'),
        'charset' => 'utf8mb4',
    ],
    
    // Session
    'session' => [
        'name' => env('SESSION_NAME', 'gymbro_session'),
        'lifetime' => env('SESSION_LIFETIME', 7200),
        'secure' => env('SESSION_SECURE', false),
    ],
    
    // Upload limits
    'uploads' => [
        'max_image_size' => 10 * 1024 * 1024,  // 10MB
        'max_video_size' => 50 * 1024 * 1024,  // 50MB
        'allowed_image_types' => ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
        'allowed_video_types' => ['video/mp4', 'video/webm'],
        'posts_path' => '/uploads/posts/',
    ],
    
    // Security
    'security' => [
        'password_min_length' => 8,
        'bcrypt_cost' => 12,
        'rate_limit_requests' => 60,
        'rate_limit_window' => 60,
    ],
];
