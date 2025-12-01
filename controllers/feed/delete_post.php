<?php

require_once __DIR__ . '/../../src/bootstrap.php';

startSession();
requireLogin();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Validate CSRF token
if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
    exit;
}

$postId = (int)($_POST['post_id'] ?? 0);

if ($postId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid post ID']);
    exit;
}

$pdo = getDb();
$currentUser = getCurrentUser();

// Check if post exists and belongs to current user
$stmt = $pdo->prepare('SELECT id, user_id, media_urls FROM posts WHERE id = ?');
$stmt->execute([$postId]);
$post = $stmt->fetch();

if (!$post) {
    echo json_encode(['success' => false, 'message' => 'Post not found']);
    exit;
}

if ((int)$post['user_id'] !== (int)$currentUser['id']) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'You can only delete your own posts']);
    exit;
}

try {
    // Delete media files if any
    if (!empty($post['media_urls'])) {
        $mediaUrls = json_decode($post['media_urls'], true);
        if (is_array($mediaUrls)) {
            foreach ($mediaUrls as $url) {
                $filePath = __DIR__ . '/../../public' . $url;
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
        }
    }
    
    // Delete the post (CASCADE will handle related records)
    $stmt = $pdo->prepare('DELETE FROM posts WHERE id = ?');
    $stmt->execute([$postId]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Post deleted successfully'
    ]);
} catch (PDOException $e) {
    error_log('Delete post error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Failed to delete post']);
}
