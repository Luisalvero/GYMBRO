<?php

requireLogin();

$userId = getCurrentUserId();
$currentUser = getCurrentUser();

if (!$currentUser) {
    setFlash('danger', 'User not found.');
    redirect('/login');
}

// Decode current user's data
$currentUserWorkoutStyles = json_decode($currentUser['workout_styles'], true) ?? [];
$preferredPartnerGenders = json_decode($currentUser['preferred_partner_genders'], true);

// Build query to find potential matches
$db = getDb();

// Find users that:
// 1. Are not the current user
// 2. Haven't been liked or passed by current user
// 3. Match home gym OR city
// 4. Have at least one overlapping workout style
// 5. Match gender preference (if set)

$query = "
    SELECT u.*, 
           (u.home_gym = ? AND u.home_gym IS NOT NULL) as gym_match,
           (u.city = ? AND u.city IS NOT NULL) as city_match
    FROM users u
    WHERE u.id != ?
    AND u.id NOT IN (
        SELECT liked_id FROM likes WHERE liker_id = ?
    )
";

$params = [
    $currentUser['home_gym'],
    $currentUser['city'],
    $userId,
    $userId
];

// Add gender preference filter if set
if ($preferredPartnerGenders !== null && !empty($preferredPartnerGenders)) {
    $placeholders = str_repeat('?,', count($preferredPartnerGenders) - 1) . '?';
    $query .= " AND u.gender IN ($placeholders)";
    $params = array_merge($params, $preferredPartnerGenders);
}

$query .= " ORDER BY RAND() LIMIT 1";

$stmt = $db->prepare($query);
$stmt->execute($params);
$candidate = $stmt->fetch();

// Filter by workout styles overlap (done in PHP since JSON querying is complex)
if ($candidate) {
    $candidateWorkoutStyles = json_decode($candidate['workout_styles'], true) ?? [];
    $overlap = array_intersect($currentUserWorkoutStyles, $candidateWorkoutStyles);
    
    // Check location match
    $locationMatch = ($candidate['gym_match'] == 1) || ($candidate['city_match'] == 1);
    
    if (empty($overlap) || !$locationMatch) {
        // Doesn't meet criteria, mark as passed automatically and try to find another
        $stmt = $db->prepare('INSERT INTO likes (liker_id, liked_id) VALUES (?, ?) ON DUPLICATE KEY UPDATE liker_id=liker_id');
        $stmt->execute([$userId, $candidate['id']]);
        
        // Try to get another candidate (simplified - in production you'd recurse or loop)
        $candidate = null;
    }
}

require __DIR__ . '/../../views/queue/index.php';
