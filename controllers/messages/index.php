<?php
/**
 * Messages Index - List all conversations
 * Security: Only shows conversations where user is a participant
 */

requireLogin();

$currentUser = getCurrentUser();
$userId = getCurrentUserId();
$db = getDb();

// Get all conversations for this user with last message and unread count
// SECURITY: Only fetch conversations from matches where user is participant
$stmt = $db->prepare("
    SELECT 
        c.id as conversation_id,
        c.match_id,
        c.updated_at,
        m.user1_id,
        m.user2_id,
        CASE 
            WHEN m.user1_id = ? THEN u2.id 
            ELSE u1.id 
        END as other_user_id,
        CASE 
            WHEN m.user1_id = ? THEN u2.name 
            ELSE u1.name 
        END as other_user_name,
        (
            SELECT content FROM messages 
            WHERE conversation_id = c.id 
            ORDER BY created_at DESC LIMIT 1
        ) as last_message,
        (
            SELECT created_at FROM messages 
            WHERE conversation_id = c.id 
            ORDER BY created_at DESC LIMIT 1
        ) as last_message_time,
        (
            SELECT COUNT(*) FROM messages 
            WHERE conversation_id = c.id 
            AND sender_id != ? 
            AND read_at IS NULL
        ) as unread_count
    FROM conversations c
    JOIN matches m ON c.match_id = m.id
    JOIN users u1 ON m.user1_id = u1.id
    JOIN users u2 ON m.user2_id = u2.id
    WHERE m.user1_id = ? OR m.user2_id = ?
    ORDER BY c.updated_at DESC
");

$stmt->execute([$userId, $userId, $userId, $userId, $userId]);
$conversations = $stmt->fetchAll();

require __DIR__ . '/../../views/messages/index.php';
