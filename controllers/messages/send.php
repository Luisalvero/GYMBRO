<?php
/**
 * Send Message API
 * Security: Verifies user is participant in the conversation
 * Returns JSON response
 */

header('Content-Type: application/json');

requireLogin();

$userId = getCurrentUserId();
$db = getDb();

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$conversationId = (int)($_POST['conversation_id'] ?? 0);
$content = trim($_POST['content'] ?? '');

// Validate input
if ($conversationId <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid conversation']);
    exit;
}

if (empty($content)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Message cannot be empty']);
    exit;
}

if (strlen($content) > 500) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Message too long (max 500 characters)']);
    exit;
}

// SECURITY: Verify user is a participant in this conversation
$stmt = $db->prepare("
    SELECT c.id
    FROM conversations c
    JOIN matches m ON c.match_id = m.id
    WHERE c.id = ? AND (m.user1_id = ? OR m.user2_id = ?)
");
$stmt->execute([$conversationId, $userId, $userId]);

if (!$stmt->fetch()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Access denied']);
    exit;
}

// Insert the message
try {
    $stmt = $db->prepare("
        INSERT INTO messages (conversation_id, sender_id, content, created_at)
        VALUES (?, ?, ?, NOW())
    ");
    $stmt->execute([$conversationId, $userId, $content]);
    
    $messageId = $db->lastInsertId();
    
    echo json_encode([
        'success' => true,
        'message' => [
            'id' => $messageId,
            'sender_id' => $userId,
            'content' => htmlspecialchars($content),
            'created_at' => date('Y-m-d H:i:s')
        ]
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to send message']);
}
