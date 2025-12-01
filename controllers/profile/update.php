<?php

requireLogin();

if (!isset($_POST['csrf_token']) || !validateCsrfToken($_POST['csrf_token'])) {
    setFlash('danger', 'Invalid request. Please try again.');
    redirect('/profile/edit');
}

$userId = getCurrentUserId();
$short_bio = trim($_POST['short_bio'] ?? '');
$home_gym = trim($_POST['home_gym'] ?? '');
$city = trim($_POST['city'] ?? '');
$preferred_partner_genders = $_POST['preferred_partner_genders'] ?? [];

$errors = [];

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
    $validGenders = ['male', 'female', 'nonbinary', 'prefer_not_to_say'];
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
        SET short_bio = ?, home_gym = ?, city = ?, preferred_partner_genders = ?
        WHERE id = ?
    ');
    
    $stmt->execute([
        $short_bio ?: null,
        $home_gym ?: null,
        $city ?: null,
        $preferred_partner_genders_json,
        $userId
    ]);
    
    setFlash('success', 'Profile updated successfully!');
    redirect('/profile');
    
} catch (PDOException $e) {
    error_log('Profile update error: ' . $e->getMessage());
    setFlash('danger', 'An error occurred while updating your profile.');
    redirect('/profile/edit');
}
