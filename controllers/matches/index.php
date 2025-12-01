<?php

requireLogin();

$userId = getCurrentUserId();
$db = getDb();

// Get all matches for current user
$stmt = $db->prepare('
    SELECT 
        m.id as match_id,
        m.created_at,
        u.id as user_id,
        u.name,
        u.age,
        u.pronouns,
        u.gender,
        u.workout_styles,
        u.home_gym,
        u.city,
        u.short_bio
    FROM matches m
    JOIN users u ON (
        (m.user1_id = ? AND u.id = m.user2_id) OR
        (m.user2_id = ? AND u.id = m.user1_id)
    )
    ORDER BY m.created_at DESC
');
$stmt->execute([$userId, $userId]);
$matches = $stmt->fetchAll();

// Decode JSON fields
foreach ($matches as &$match) {
    $match['workout_styles'] = json_decode($match['workout_styles'], true) ?? [];
}

require __DIR__ . '/../../views/matches/index.php';
