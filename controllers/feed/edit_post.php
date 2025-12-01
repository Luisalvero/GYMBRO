<?php

require_once __DIR__ . '/../../src/helpers.php';

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
$content = trim($_POST['content'] ?? '');
$title = trim($_POST['title'] ?? '');

if ($postId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid post ID']);
    exit;
}

$pdo = getDb();
$currentUser = getCurrentUser($pdo);

// Check if post exists and belongs to current user
$stmt = $pdo->prepare('SELECT id, user_id, post_type FROM posts WHERE id = ?');
$stmt->execute([$postId]);
$post = $stmt->fetch();

if (!$post) {
    echo json_encode(['success' => false, 'message' => 'Post not found']);
    exit;
}

if ($post['user_id'] !== $currentUser['id']) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'You can only edit your own posts']);
    exit;
}

// Update the post
try {
    if ($post['post_type'] === 'forum' || $post['post_type'] === 'meetup') {
        $stmt = $pdo->prepare('UPDATE posts SET content = ?, title = ?, updated_at = NOW() WHERE id = ?');
        $stmt->execute([$content, $title, $postId]);
    } else {
        $stmt = $pdo->prepare('UPDATE posts SET content = ?, updated_at = NOW() WHERE id = ?');
        $stmt->execute([$content, $postId]);
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Post updated successfully',
        'content' => $content,
        'title' => $title
    ]);
} catch (PDOException $e) {
    error_log('Edit post error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Failed to update post']);
}
