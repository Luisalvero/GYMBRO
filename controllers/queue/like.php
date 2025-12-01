<?php

requireLogin();

if (!isset($_POST['csrf_token']) || !validateCsrfToken($_POST['csrf_token'])) {
    jsonResponse(['success' => false, 'message' => 'Invalid request'], 400);
}

$userId = getCurrentUserId();
$likedUserId = intval($_POST['liked_user_id'] ?? 0);

if ($likedUserId <= 0) {
    jsonResponse(['success' => false, 'message' => 'Invalid user'], 400);
}

try {
    $db = getDb();
    
    // Get liked user's name for potential match display
    $stmt = $db->prepare('SELECT name FROM users WHERE id = ?');
    $stmt->execute([$likedUserId]);
    $likedUser = $stmt->fetch();
    $likedUserName = $likedUser ? $likedUser['name'] : 'your new bro';
    
    // Insert like
    $stmt = $db->prepare('INSERT INTO likes (liker_id, liked_id) VALUES (?, ?) ON DUPLICATE KEY UPDATE liker_id=liker_id');
    $stmt->execute([$userId, $likedUserId]);
    
    // Check if it's a mutual like (match)
    $stmt = $db->prepare('SELECT id FROM likes WHERE liker_id = ? AND liked_id = ?');
    $stmt->execute([$likedUserId, $userId]);
    $mutualLike = $stmt->fetch();
    
    $isMatch = false;
    if ($mutualLike) {
        // Create match (ensure user1_id < user2_id for unique constraint)
        $user1 = min($userId, $likedUserId);
        $user2 = max($userId, $likedUserId);
        
        $stmt = $db->prepare('INSERT INTO matches (user1_id, user2_id) VALUES (?, ?) ON DUPLICATE KEY UPDATE user1_id=user1_id');
        $stmt->execute([$user1, $user2]);
        $isMatch = true;
    }
    
    jsonResponse([
        'success' => true,
        'is_match' => $isMatch,
        'matched_user_name' => $isMatch ? $likedUserName : null,
        'message' => $isMatch ? "It's a match! 🎉" : 'Like sent!'
    ]);
    
} catch (PDOException $e) {
    error_log('Like error: ' . $e->getMessage());
    jsonResponse(['success' => false, 'message' => 'An error occurred'], 500);
}
