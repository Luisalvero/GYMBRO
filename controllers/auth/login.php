<?php

if ($requestMethod === 'GET') {
    if (isLoggedIn()) {
        redirect('/feed');
    }
    require __DIR__ . '/../../views/auth/login.php';
    exit;
}

// POST - Handle login
if (!isset($_POST['csrf_token']) || !validateCsrfToken($_POST['csrf_token'])) {
    setFlash('danger', 'Invalid request. Please try again.');
    redirect('/login');
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

$errors = [];

if (empty($email)) {
    $errors[] = 'Email is required.';
}

if (empty($password)) {
    $errors[] = 'Password is required.';
}

if (empty($errors)) {
    $db = getDb();
    $stmt = $db->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        setFlash('success', 'Welcome back, ' . $user['name'] . '!');
        redirect('/feed');
    } else {
        $errors[] = 'Invalid email or password.';
    }
}

if (!empty($errors)) {
    $_SESSION['login_errors'] = $errors;
    $_SESSION['old_email'] = $email;
    redirect('/login');
}
