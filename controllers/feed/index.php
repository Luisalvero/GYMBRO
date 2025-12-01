<?php

requireLogin();

$currentUser = getCurrentUser();
$userId = getCurrentUserId();

// Get filter from query string
$filter = $_GET['filter'] ?? 'all';
$validFilters = ['all', 'achievement', 'media', 'forum', 'meetup'];
if (!in_array($filter, $validFilters)) {
    $filter = 'all';
}

$db = getDb();

// Build query based on filter
$query = "
    SELECT p.*, u.name as user_name, u.home_gym as user_gym,
           (SELECT COUNT(*) FROM post_likes WHERE post_id = p.id) as like_count,
           (SELECT COUNT(*) FROM post_comments WHERE post_id = p.id) as comment_count,
           (SELECT COUNT(*) FROM post_likes WHERE post_id = p.id AND user_id = ?) as user_liked
    FROM posts p
    JOIN users u ON p.user_id = u.id
";

$params = [$userId];

if ($filter !== 'all') {
    $query .= " WHERE p.post_type = ?";
    $params[] = $filter;
}

$query .= " ORDER BY p.created_at DESC LIMIT 50";

$stmt = $db->prepare($query);
$stmt->execute($params);
$posts = $stmt->fetchAll();

// Get comments for each post (last 3)
foreach ($posts as &$post) {
    $stmt = $db->prepare('
        SELECT c.*, u.name as user_name 
        FROM post_comments c 
        JOIN users u ON c.user_id = u.id 
        WHERE c.post_id = ? 
        ORDER BY c.created_at DESC 
        LIMIT 3
    ');
    $stmt->execute([$post['id']]);
    $post['comments'] = array_reverse($stmt->fetchAll());
    
    // Decode media URLs
    $post['media_urls'] = json_decode($post['media_urls'] ?? '[]', true) ?? [];
    
    // Get meetup attendees if applicable
    if ($post['post_type'] === 'meetup') {
        $stmt = $db->prepare('
            SELECT ma.status, u.name as user_name
            FROM meetup_attendees ma
            JOIN users u ON ma.user_id = u.id
            WHERE ma.post_id = ?
            ORDER BY ma.created_at DESC
        ');
        $stmt->execute([$post['id']]);
        $post['attendees'] = $stmt->fetchAll();
        
        // Check user's RSVP status
        $stmt = $db->prepare('SELECT status FROM meetup_attendees WHERE post_id = ? AND user_id = ?');
        $stmt->execute([$post['id'], $userId]);
        $userRsvp = $stmt->fetch();
        $post['user_rsvp'] = $userRsvp ? $userRsvp['status'] : null;
    }
}

require __DIR__ . '/../../views/feed/index.php';
