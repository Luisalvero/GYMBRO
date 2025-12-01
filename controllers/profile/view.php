<?php

requireLogin();

$user = getCurrentUser();

if (!$user) {
    setFlash('danger', 'User not found.');
    redirect('/login');
}

// Decode JSON fields
$user['workout_styles'] = json_decode($user['workout_styles'], true) ?? [];
$user['preferred_partner_genders'] = json_decode($user['preferred_partner_genders'], true) ?? [];

require __DIR__ . '/../../views/profile/view.php';
