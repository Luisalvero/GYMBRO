<?php
/**
 * Fetch Messages API (Polling endpoint)
 * Security: Verifies user is participant in the conversation
 * Returns JSON with new messages since a given message ID
 */

header('Content-Type: application/json');

requireLogin();

$userId = getCurrentUserId();
$db = getDb();

$conversationId = (int)($_GET['conversation_id'] ?? 0);
$lastMessageId = (int)($_GET['last_message_id'] ?? 0);

// Validate input
if ($conversationId <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid conversation']);
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

// Mark messages from other user as read
$stmt = $db->prepare("
    UPDATE messages 
    SET read_at = NOW() 
    WHERE conversation_id = ? 
    AND sender_id != ? 
    AND read_at IS NULL
");
$stmt->execute([$conversationId, $userId]);

// Fetch new messages since last_message_id
$stmt = $db->prepare("
    SELECT 
        msg.id,
        msg.sender_id,
        msg.content,
        msg.read_at,
        msg.created_at,
        u.name as sender_name
    FROM messages msg
    JOIN users u ON msg.sender_id = u.id
    WHERE msg.conversation_id = ? AND msg.id > ?
    ORDER BY msg.created_at ASC
");
$stmt->execute([$conversationId, $lastMessageId]);
$messages = $stmt->fetchAll();

// Escape content for safe HTML rendering
$messages = array_map(function($msg) {
    $msg['content'] = htmlspecialchars($msg['content']);
    return $msg;
}, $messages);

echo json_encode([
    'success' => true,
    'messages' => $messages
]);
