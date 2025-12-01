<?php

requireLogin();

if (!isset($_POST['csrf_token']) || !validateCsrfToken($_POST['csrf_token'])) {
    jsonResponse(['success' => false, 'message' => 'Invalid request'], 400);
}

$userId = getCurrentUserId();
$postType = $_POST['post_type'] ?? '';
$title = trim($_POST['title'] ?? '');
$content = trim($_POST['content'] ?? '');

$validPostTypes = ['achievement', 'media', 'forum', 'meetup'];
if (!in_array($postType, $validPostTypes)) {
    jsonResponse(['success' => false, 'message' => 'Invalid post type'], 400);
}

$errors = [];

// Validate based on post type
switch ($postType) {
    case 'achievement':
        $achievementType = trim($_POST['achievement_type'] ?? '');
        $achievementValue = trim($_POST['achievement_value'] ?? '');
        if (empty($achievementType)) {
            $errors[] = 'Achievement type is required';
        }
        if (empty($content)) {
            $errors[] = 'Description is required';
        }
        break;
        
    case 'media':
        if (empty($_FILES['media']) || $_FILES['media']['error'][0] !== UPLOAD_ERR_OK) {
            $errors[] = 'At least one photo or video is required';
        }
        break;
        
    case 'forum':
        if (empty($title)) {
            $errors[] = 'Title is required';
        }
        if (empty($content)) {
            $errors[] = 'Content is required';
        }
        break;
        
    case 'meetup':
        if (empty($title)) {
            $errors[] = 'Event title is required';
        }
        $meetupDatetime = $_POST['meetup_datetime'] ?? '';
        $meetupLocationName = trim($_POST['meetup_location_name'] ?? '');
        $meetupLatitude = $_POST['meetup_latitude'] ?? null;
        $meetupLongitude = $_POST['meetup_longitude'] ?? null;
        
        if (empty($meetupDatetime)) {
            $errors[] = 'Event date and time is required';
        }
        if (empty($meetupLocationName)) {
            $errors[] = 'Location is required';
        }
        break;
}

if (!empty($errors)) {
    jsonResponse(['success' => false, 'message' => implode(', ', $errors)], 400);
}

// Handle file uploads for media posts
$mediaUrls = [];
if ($postType === 'media' && !empty($_FILES['media'])) {
    $uploadDir = __DIR__ . '/../../public/uploads/posts/';
    
    $files = $_FILES['media'];
    $fileCount = count($files['name']);
    
    for ($i = 0; $i < $fileCount && $i < 10; $i++) { // Max 10 files
        if ($files['error'][$i] !== UPLOAD_ERR_OK) continue;
        
        $tmpName = $files['tmp_name'][$i];
        $originalName = $files['name'][$i];
        $fileSize = $files['size'][$i];
        $mimeType = mime_content_type($tmpName);
        
        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'video/mp4', 'video/webm'];
        if (!in_array($mimeType, $allowedTypes)) {
            continue;
        }
        
        // Validate file size (max 50MB for videos, 10MB for images)
        $maxSize = strpos($mimeType, 'video') !== false ? 50 * 1024 * 1024 : 10 * 1024 * 1024;
        if ($fileSize > $maxSize) {
            continue;
        }
        
        // Generate unique filename
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $newFilename = uniqid('post_' . $userId . '_') . '.' . $extension;
        $destination = $uploadDir . $newFilename;
        
        if (move_uploaded_file($tmpName, $destination)) {
            $mediaUrls[] = '/uploads/posts/' . $newFilename;
        }
    }
    
    if (empty($mediaUrls)) {
        jsonResponse(['success' => false, 'message' => 'Failed to upload media files'], 400);
    }
}

try {
    $db = getDb();
    
    $stmt = $db->prepare('
        INSERT INTO posts (
            user_id, post_type, title, content,
            achievement_type, achievement_value,
            media_urls,
            meetup_datetime, meetup_location_name, meetup_latitude, meetup_longitude, meetup_max_attendees
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ');
    
    $stmt->execute([
        $userId,
        $postType,
        $title ?: null,
        $content ?: null,
        $postType === 'achievement' ? ($achievementType ?? null) : null,
        $postType === 'achievement' ? ($achievementValue ?? null) : null,
        !empty($mediaUrls) ? json_encode($mediaUrls) : null,
        $postType === 'meetup' ? $meetupDatetime : null,
        $postType === 'meetup' ? $meetupLocationName : null,
        $postType === 'meetup' && $meetupLatitude ? $meetupLatitude : null,
        $postType === 'meetup' && $meetupLongitude ? $meetupLongitude : null,
        $postType === 'meetup' ? ($_POST['meetup_max_attendees'] ?? null) : null,
    ]);
    
    $postId = $db->lastInsertId();
    
    jsonResponse([
        'success' => true,
        'message' => 'Post created successfully!',
        'post_id' => $postId
    ]);
    
} catch (PDOException $e) {
    error_log('Post creation error: ' . $e->getMessage());
    jsonResponse(['success' => false, 'message' => 'Failed to create post'], 500);
}
