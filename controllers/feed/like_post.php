<?php

requireLogin();

if (!isset($_POST['csrf_token']) || !validateCsrfToken($_POST['csrf_token'])) {
    jsonResponse(['success' => false, 'message' => 'Invalid request'], 400);
}

$userId = getCurrentUserId();
$postId = intval($_POST['post_id'] ?? 0);
$reactionType = $_POST['reaction_type'] ?? 'fire';

if ($postId <= 0) {
    jsonResponse(['success' => false, 'message' => 'Invalid post'], 400);
}

$validReactions = ['fire', 'muscle', 'heart', 'clap'];
if (!in_array($reactionType, $validReactions)) {
    $reactionType = 'fire';
}

try {
    $db = getDb();
    
    // Check if already liked
    $stmt = $db->prepare('SELECT id FROM post_likes WHERE post_id = ? AND user_id = ?');
    $stmt->execute([$postId, $userId]);
    $existingLike = $stmt->fetch();
    
    if ($existingLike) {
        // Unlike
        $stmt = $db->prepare('DELETE FROM post_likes WHERE post_id = ? AND user_id = ?');
        $stmt->execute([$postId, $userId]);
        $liked = false;
    } else {
        // Like
        $stmt = $db->prepare('INSERT INTO post_likes (post_id, user_id, reaction_type) VALUES (?, ?, ?)');
        $stmt->execute([$postId, $userId, $reactionType]);
        $liked = true;
    }
    
    // Get updated like count
    $stmt = $db->prepare('SELECT COUNT(*) as count FROM post_likes WHERE post_id = ?');
    $stmt->execute([$postId]);
    $likeCount = $stmt->fetch()['count'];
    
    jsonResponse([
        'success' => true,
        'liked' => $liked,
        'like_count' => $likeCount
    ]);
    
} catch (PDOException $e) {
    error_log('Like error: ' . $e->getMessage());
    jsonResponse(['success' => false, 'message' => 'Failed to update like'], 500);
}
