<?php
/**
 * View Conversation - Chat interface
 * Security: Verifies user is participant in the conversation
 */

requireLogin();

$currentUser = getCurrentUser();
$userId = getCurrentUserId();
$db = getDb();

$conversationId = (int)($_GET['id'] ?? 0);

if ($conversationId <= 0) {
    redirect('/messages');
}

// SECURITY: Verify user is a participant in this conversation
$stmt = $db->prepare("
    SELECT 
        c.id as conversation_id,
        c.match_id,
        m.user1_id,
        m.user2_id,
        CASE 
            WHEN m.user1_id = ? THEN u2.id 
            ELSE u1.id 
        END as other_user_id,
        CASE 
            WHEN m.user1_id = ? THEN u2.name 
            ELSE u1.name 
        END as other_user_name
    FROM conversations c
    JOIN matches m ON c.match_id = m.id
    JOIN users u1 ON m.user1_id = u1.id
    JOIN users u2 ON m.user2_id = u2.id
    WHERE c.id = ? AND (m.user1_id = ? OR m.user2_id = ?)
");

$stmt->execute([$userId, $userId, $conversationId, $userId, $userId]);
$conversation = $stmt->fetch();

// SECURITY: If not a participant, redirect away
if (!$conversation) {
    redirect('/messages');
}

// Mark messages as read (only messages from the other user)
$stmt = $db->prepare("
    UPDATE messages 
    SET read_at = NOW() 
    WHERE conversation_id = ? 
    AND sender_id != ? 
    AND read_at IS NULL
");
$stmt->execute([$conversationId, $userId]);

// Fetch messages
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
    WHERE msg.conversation_id = ?
    ORDER BY msg.created_at ASC
");
$stmt->execute([$conversationId]);
$messages = $stmt->fetchAll();

require __DIR__ . '/../../views/messages/view.php';
