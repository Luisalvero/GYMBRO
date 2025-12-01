<?php

require_once __DIR__ . '/../src/helpers.php';

startSession();

// Simple router
$requestUri = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Parse URI and remove query string
$uri = parse_url($requestUri, PHP_URL_PATH);
$uri = rtrim($uri, '/');
if (empty($uri)) {
    $uri = '/';
}

// Define routes
$routes = [
    'GET' => [
        '/' => 'controllers/home.php',
        '/login' => 'controllers/auth/login.php',
        '/signup' => 'controllers/auth/signup.php',
        '/logout' => 'controllers/auth/logout.php',
        '/profile' => 'controllers/profile/view.php',
        '/profile/edit' => 'controllers/profile/edit.php',
        '/queue' => 'controllers/queue/index.php',
        '/feed' => 'controllers/feed/index.php',
        '/matches' => 'controllers/matches/index.php',
    ],
    'POST' => [
        '/login' => 'controllers/auth/login.php',
        '/signup' => 'controllers/auth/signup.php',
        '/profile/update' => 'controllers/profile/update.php',
        '/queue/like' => 'controllers/queue/like.php',
        '/queue/pass' => 'controllers/queue/pass.php',
    ],
];

// Route matching
if (isset($routes[$requestMethod][$uri])) {
    $controllerFile = __DIR__ . '/../' . $routes[$requestMethod][$uri];
    if (file_exists($controllerFile)) {
        require $controllerFile;
    } else {
        http_response_code(404);
        require __DIR__ . '/../views/errors/404.php';
    }
} else {
    http_response_code(404);
    require __DIR__ . '/../views/errors/404.php';
}
