<?php

requireLogin();

if (!isset($_POST['csrf_token']) || !validateCsrfToken($_POST['csrf_token'])) {
    jsonResponse(['success' => false, 'message' => 'Invalid request'], 400);
}

$userId = getCurrentUserId();
$postId = intval($_POST['post_id'] ?? 0);
$status = $_POST['status'] ?? 'interested';

if ($postId <= 0) {
    jsonResponse(['success' => false, 'message' => 'Invalid post'], 400);
}

$validStatuses = ['going', 'interested', 'not_going'];
if (!in_array($status, $validStatuses)) {
    $status = 'interested';
}

try {
    $db = getDb();
    
    // Verify it's a meetup post
    $stmt = $db->prepare('SELECT id, meetup_max_attendees FROM posts WHERE id = ? AND post_type = "meetup"');
    $stmt->execute([$postId]);
    $post = $stmt->fetch();
    
    if (!$post) {
        jsonResponse(['success' => false, 'message' => 'Invalid meetup'], 400);
    }
    
    // Check if removing RSVP
    if ($status === 'not_going') {
        $stmt = $db->prepare('DELETE FROM meetup_attendees WHERE post_id = ? AND user_id = ?');
        $stmt->execute([$postId, $userId]);
    } else {
        // Check max attendees if going
        if ($status === 'going' && $post['meetup_max_attendees']) {
            $stmt = $db->prepare('SELECT COUNT(*) as count FROM meetup_attendees WHERE post_id = ? AND status = "going"');
            $stmt->execute([$postId]);
            $goingCount = $stmt->fetch()['count'];
            
            if ($goingCount >= $post['meetup_max_attendees']) {
                jsonResponse(['success' => false, 'message' => 'Event is full'], 400);
            }
        }
        
        // Insert or update RSVP
        $stmt = $db->prepare('
            INSERT INTO meetup_attendees (post_id, user_id, status) 
            VALUES (?, ?, ?) 
            ON DUPLICATE KEY UPDATE status = ?
        ');
        $stmt->execute([$postId, $userId, $status, $status]);
    }
    
    // Get updated counts
    $stmt = $db->prepare('
        SELECT 
            SUM(status = "going") as going_count,
            SUM(status = "interested") as interested_count
        FROM meetup_attendees WHERE post_id = ?
    ');
    $stmt->execute([$postId]);
    $counts = $stmt->fetch();
    
    jsonResponse([
        'success' => true,
        'status' => $status,
        'going_count' => (int)$counts['going_count'],
        'interested_count' => (int)$counts['interested_count']
    ]);
    
} catch (PDOException $e) {
    error_log('RSVP error: ' . $e->getMessage());
    jsonResponse(['success' => false, 'message' => 'Failed to update RSVP'], 500);
}
