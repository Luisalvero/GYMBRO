<?php

if ($requestMethod === 'GET') {
    if (isLoggedIn()) {
        redirect('/feed');
    }
    require __DIR__ . '/../../views/auth/signup.php';
    exit;
}

// POST - Handle signup
if (!isset($_POST['csrf_token']) || !validateCsrfToken($_POST['csrf_token'])) {
    setFlash('danger', 'Invalid request. Please try again.');
    redirect('/signup');
}

$name = trim($_POST['name'] ?? '');
$age = trim($_POST['age'] ?? '');
$pronouns = $_POST['pronouns'] ?? '';
$gender = $_POST['gender'] ?? '';
$activity_level = $_POST['activity_level'] ?? null;
$workout_styles = $_POST['workout_styles'] ?? [];
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$password_confirm = $_POST['password_confirm'] ?? '';

$errors = [];

// Validation
if (empty($name)) {
    $errors[] = 'Name is required.';
}

if (!validateAge($age)) {
    $errors[] = 'Age must be between 13 and 100.';
}

$validPronouns = ['he/him', 'she/her', 'they/them', 'prefer_not_to_say'];
if (!in_array($pronouns, $validPronouns)) {
    $errors[] = 'Please select valid pronouns.';
}

$validGenders = ['male', 'female', 'nonbinary', 'prefer_not_to_say'];
if (!in_array($gender, $validGenders)) {
    $errors[] = 'Please select a valid gender.';
}

if ($activity_level !== null && $activity_level !== '') {
    $validActivityLevels = ['not_very_active', 'kinda_active', 'super_gymbro'];
    if (!in_array($activity_level, $validActivityLevels)) {
        $errors[] = 'Please select a valid activity level.';
    }
} else {
    $activity_level = null;
}

if (!validateWorkoutStyles($workout_styles)) {
    $errors[] = 'Please select at least one valid workout style.';
}

if (!validateEmail($email)) {
    $errors[] = 'Please enter a valid email address.';
}

if (!validatePassword($password)) {
    $errors[] = 'Password must be at least 8 characters with 1 uppercase letter, 2 digits, and 2 symbols from @#!?';
}

if ($password !== $password_confirm) {
    $errors[] = 'Passwords do not match.';
}

// Check if email already exists
if (empty($errors)) {
    $db = getDb();
    $stmt = $db->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        $errors[] = 'Email address is already registered.';
    }
}

if (!empty($errors)) {
    $_SESSION['signup_errors'] = $errors;
    $_SESSION['old_input'] = $_POST;
    redirect('/signup');
}

// Create user
try {
    $db = getDb();
    $stmt = $db->prepare('
        INSERT INTO users (name, age, pronouns, gender, activity_level, workout_styles, email, password_hash)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ');
    
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $workoutStylesJson = json_encode($workout_styles);
    
    $stmt->execute([
        $name,
        $age,
        $pronouns,
        $gender,
        $activity_level,
        $workoutStylesJson,
        $email,
        $passwordHash
    ]);
    
    $userId = $db->lastInsertId();
    
    // Regenerate session to prevent session fixation attacks
    regenerateSessionOnAuth();
    
    // Log the user in
    $_SESSION['user_id'] = $userId;
    $_SESSION['user_name'] = $name;
    
    setFlash('success', 'Welcome to GymBro, ' . $name . '! Complete your profile to find your bros.');
    redirect('/profile/edit');
    
} catch (PDOException $e) {
    error_log('Signup error: ' . $e->getMessage());
    setFlash('danger', 'An error occurred during signup. Please try again.');
    redirect('/signup');
}
