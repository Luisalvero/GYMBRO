<?php

requireLogin();

if (!isset($_POST['csrf_token']) || !validateCsrfToken($_POST['csrf_token'])) {
    setFlash('danger', 'Invalid request. Please try again.');
    redirect('/profile/edit');
}

$userId = getCurrentUserId();

// Get all form fields
$name = trim($_POST['name'] ?? '');
$age = trim($_POST['age'] ?? '');
$pronouns = $_POST['pronouns'] ?? '';
$gender = $_POST['gender'] ?? '';
$email = trim($_POST['email'] ?? '');
$activity_level = $_POST['activity_level'] ?? null;
$workout_styles = $_POST['workout_styles'] ?? [];
$short_bio = trim($_POST['short_bio'] ?? '');
$home_gym = trim($_POST['home_gym'] ?? '');
$city = trim($_POST['city'] ?? '');
$preferred_partner_genders = $_POST['preferred_partner_genders'] ?? [];

$errors = [];

// Required field validation
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

if (!validateEmail($email)) {
    $errors[] = 'Please enter a valid email address.';
}

// Check if email is taken by another user
if (!empty($email)) {
    $db = getDb();
    $stmt = $db->prepare('SELECT id FROM users WHERE email = ? AND id != ?');
    $stmt->execute([$email, $userId]);
    if ($stmt->fetch()) {
        $errors[] = 'Email address is already taken by another user.';
    }
}

// Activity level validation (optional)
if ($activity_level !== null && $activity_level !== '') {
    $validActivityLevels = ['not_very_active', 'kinda_active', 'super_gymbro'];
    if (!in_array($activity_level, $validActivityLevels)) {
        $errors[] = 'Please select a valid activity level.';
    }
} else {
    $activity_level = null;
}

// Workout styles validation
if (!validateWorkoutStyles($workout_styles)) {
    $errors[] = 'Please select at least one valid workout style.';
}

// Optional validation
if (!empty($short_bio) && strlen($short_bio) > 500) {
    $errors[] = 'Bio must be less than 500 characters.';
}

if (!empty($home_gym) && strlen($home_gym) > 255) {
    $errors[] = 'Home gym must be less than 255 characters.';
}

if (!empty($city) && strlen($city) > 100) {
    $errors[] = 'City must be less than 100 characters.';
}

// Validate preferred_partner_genders
if (!empty($preferred_partner_genders)) {
    foreach ($preferred_partner_genders as $g) {
        if (!in_array($g, $validGenders)) {
            $errors[] = 'Invalid gender preference selected.';
            break;
        }
    }
    $preferred_partner_genders_json = json_encode($preferred_partner_genders);
} else {
    $preferred_partner_genders_json = null; // null = any
}

if (!empty($errors)) {
    $_SESSION['profile_errors'] = $errors;
    redirect('/profile/edit');
}

// Update profile
try {
    $db = getDb();
    $stmt = $db->prepare('
        UPDATE users 
        SET name = ?, age = ?, pronouns = ?, gender = ?, email = ?, 
            activity_level = ?, workout_styles = ?,
            short_bio = ?, home_gym = ?, city = ?, preferred_partner_genders = ?
        WHERE id = ?
    ');
    
    $workoutStylesJson = json_encode($workout_styles);
    
    $stmt->execute([
        $name,
        $age,
        $pronouns,
        $gender,
        $email,
        $activity_level,
        $workoutStylesJson,
        $short_bio ?: null,
        $home_gym ?: null,
        $city ?: null,
        $preferred_partner_genders_json,
        $userId
    ]);
    
    // Update session name if changed
    $_SESSION['user_name'] = $name;
    
    setFlash('success', 'Profile updated successfully!');
    redirect('/profile');
    
} catch (PDOException $e) {
    error_log('Profile update error: ' . $e->getMessage());
    setFlash('danger', 'An error occurred while updating your profile.');
    redirect('/profile/edit');
}
