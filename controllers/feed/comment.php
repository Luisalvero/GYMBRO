<?php

requireLogin();

if (!isset($_POST['csrf_token']) || !validateCsrfToken($_POST['csrf_token'])) {
    jsonResponse(['success' => false, 'message' => 'Invalid request'], 400);
}

$userId = getCurrentUserId();
$postId = intval($_POST['post_id'] ?? 0);
$content = trim($_POST['content'] ?? '');

if ($postId <= 0) {
    jsonResponse(['success' => false, 'message' => 'Invalid post'], 400);
}

if (empty($content)) {
    jsonResponse(['success' => false, 'message' => 'Comment cannot be empty'], 400);
}

if (strlen($content) > 1000) {
    jsonResponse(['success' => false, 'message' => 'Comment is too long'], 400);
}

try {
    $db = getDb();
    
    $stmt = $db->prepare('INSERT INTO post_comments (post_id, user_id, content) VALUES (?, ?, ?)');
    $stmt->execute([$postId, $userId, $content]);
    
    $commentId = $db->lastInsertId();
    
    // Get user info for response
    $currentUser = getCurrentUser();
    
    jsonResponse([
        'success' => true,
        'comment' => [
            'id' => $commentId,
            'content' => $content,
            'user_name' => $currentUser['name'],
            'created_at' => date('M j, Y g:i A')
        ]
    ]);
    
} catch (PDOException $e) {
    error_log('Comment error: ' . $e->getMessage());
    jsonResponse(['success' => false, 'message' => 'Failed to add comment'], 500);
}
