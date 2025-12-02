<?php
/**
 * GymBro Application Entry Point
 * 
 * All requests are routed through this file
 */

require_once __DIR__ . '/../src/bootstrap.php';

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
        '/messages' => 'controllers/messages/index.php',
        '/messages/view' => 'controllers/messages/view.php',
        '/messages/fetch' => 'controllers/messages/fetch.php',
    ],
    'POST' => [
        '/login' => 'controllers/auth/login.php',
        '/signup' => 'controllers/auth/signup.php',
        '/profile/update' => 'controllers/profile/update.php',
        '/queue/like' => 'controllers/queue/like.php',
        '/queue/pass' => 'controllers/queue/pass.php',
        '/feed/post' => 'controllers/feed/create_post.php',
        '/feed/like' => 'controllers/feed/like_post.php',
        '/feed/comment' => 'controllers/feed/comment.php',
        '/feed/rsvp' => 'controllers/feed/rsvp.php',
        '/feed/edit' => 'controllers/feed/edit_post.php',
        '/feed/delete' => 'controllers/feed/delete_post.php',
        '/messages/send' => 'controllers/messages/send.php',
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
