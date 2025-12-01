<?php

requireLogin();

if (!isset($_POST['csrf_token']) || !validateCsrfToken($_POST['csrf_token'])) {
    jsonResponse(['success' => false, 'message' => 'Invalid request'], 400);
}

$userId = getCurrentUserId();
$passedUserId = intval($_POST['passed_user_id'] ?? 0);

if ($passedUserId <= 0) {
    jsonResponse(['success' => false, 'message' => 'Invalid user'], 400);
}

try {
    $db = getDb();
    
    // Insert a "like" record to mark as seen/passed (same table, we just don't check for matches)
    $stmt = $db->prepare('INSERT INTO likes (liker_id, liked_id) VALUES (?, ?) ON DUPLICATE KEY UPDATE liker_id=liker_id');
    $stmt->execute([$userId, $passedUserId]);
    
    jsonResponse(['success' => true, 'message' => 'Passed']);
    
} catch (PDOException $e) {
    error_log('Pass error: ' . $e->getMessage());
    jsonResponse(['success' => false, 'message' => 'An error occurred'], 500);
}
