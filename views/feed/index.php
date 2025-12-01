<?php
$pageTitle = 'Feed - GymBro';
require __DIR__ . '/../partials/header.php';

// Helper function for time ago
function timeAgo($datetime) {
    $time = strtotime($datetime);
    $diff = time() - $time;
    
    if ($diff < 60) return 'just now';
    if ($diff < 3600) return floor($diff / 60) . 'm ago';
    if ($diff < 86400) return floor($diff / 3600) . 'h ago';
    if ($diff < 604800) return floor($diff / 86400) . 'd ago';
    return date('M j', $time);
}
?>

<!-- Leaflet CSS for meetup maps -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<div class="feed-container">
    <!-- Create Post Button -->
    <div class="create-post-bar mb-4">
        <div class="d-flex align-items-center gap-3">
            <div class="avatar-circle">
                <i class="bi bi-person-fill"></i>
            </div>
            <button class="create-post-input" data-bs-toggle="modal" data-bs-target="#createPostModal">
                What's your win today, <?= escape($currentUser['name']) ?>?
            </button>
        </div>
        <div class="quick-post-types mt-3">
            <button class="quick-post-btn" data-type="achievement" data-bs-toggle="modal" data-bs-target="#createPostModal">
                <i class="bi bi-trophy-fill"></i> Achievement
            </button>
            <button class="quick-post-btn" data-type="media" data-bs-toggle="modal" data-bs-target="#createPostModal">
                <i class="bi bi-camera-fill"></i> Photo/Video
            </button>
            <button class="quick-post-btn" data-type="forum" data-bs-toggle="modal" data-bs-target="#createPostModal">
                <i class="bi bi-chat-square-text-fill"></i> Discussion
            </button>
            <button class="quick-post-btn" data-type="meetup" data-bs-toggle="modal" data-bs-target="#createPostModal">
                <i class="bi bi-geo-alt-fill"></i> Meetup
            </button>
        </div>
    </div>

    <!-- Feed Tabs -->
    <div class="feed-tabs mb-4">
        <a href="/feed" class="feed-tab <?= $filter === 'all' ? 'active' : '' ?>">
            <i class="bi bi-grid-3x3-gap"></i> All
        </a>
        <a href="/feed?filter=achievement" class="feed-tab <?= $filter === 'achievement' ? 'active' : '' ?>">
            <i class="bi bi-trophy"></i> Achievements
        </a>
        <a href="/feed?filter=media" class="feed-tab <?= $filter === 'media' ? 'active' : '' ?>">
            <i class="bi bi-image"></i> Media
        </a>
        <a href="/feed?filter=forum" class="feed-tab <?= $filter === 'forum' ? 'active' : '' ?>">
            <i class="bi bi-chat-square-text"></i> Forum
        </a>
        <a href="/feed?filter=meetup" class="feed-tab <?= $filter === 'meetup' ? 'active' : '' ?>">
            <i class="bi bi-calendar-event"></i> Meetups
        </a>
    </div>

    <!-- Posts -->
    <div class="posts-container">
        <?php if (empty($posts)): ?>
            <div class="empty-feed">
                <div class="empty-icon">
                    <i class="bi bi-lightning-charge-fill"></i>
                </div>
                <h4>No posts yet</h4>
                <p>Be the first to share your fitness journey!</p>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createPostModal">
                    <i class="bi bi-plus-lg"></i> Create Post
                </button>
            </div>
        <?php else: ?>
            <?php foreach ($posts as $post): ?>
                <div class="post-card" data-post-id="<?= $post['id'] ?>">
                    <!-- Post Header -->
                    <div class="post-header">
                        <div class="post-avatar">
                            <i class="bi bi-person-fill"></i>
                        </div>
                        <div class="post-user-info">
                            <span class="post-username"><?= escape($post['user_name']) ?></span>
                            <span class="post-meta">
                                <?php if ($post['user_gym']): ?>
                                    <i class="bi bi-building"></i> <?= escape($post['user_gym']) ?> •
                                <?php endif; ?>
                                <?= timeAgo($post['created_at']) ?>
                            </span>
                        </div>
                        <div class="post-type-badge post-type-<?= $post['post_type'] ?>">
                            <?php
                            $typeIcons = [
                                'achievement' => 'trophy-fill',
                                'media' => 'camera-fill',
                                'forum' => 'chat-square-text-fill',
                                'meetup' => 'geo-alt-fill'
                            ];
                            ?>
                            <i class="bi bi-<?= $typeIcons[$post['post_type']] ?>"></i>
                            <?= ucfirst($post['post_type']) ?>
                        </div>
                        <?php if ($post['user_id'] === $currentUser['id']): ?>
                        <div class="post-menu dropdown">
                            <button class="post-menu-btn" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <button class="dropdown-item edit-post-btn" 
                                            data-post-id="<?= $post['id'] ?>"
                                            data-post-type="<?= $post['post_type'] ?>"
                                            data-content="<?= escape($post['content'] ?? '') ?>"
                                            data-title="<?= escape($post['title'] ?? '') ?>">
                                        <i class="bi bi-pencil"></i> Edit
                                    </button>
                                </li>
                                <li>
                                    <button class="dropdown-item delete-post-btn text-danger" 
                                            data-post-id="<?= $post['id'] ?>">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </li>
                            </ul>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Post Content -->
                    <div class="post-content">
                        <?php if ($post['post_type'] === 'achievement'): ?>
                            <div class="achievement-banner">
                                <div class="achievement-icon">
                                    <i class="bi bi-trophy-fill"></i>
                                </div>
                                <div class="achievement-details">
                                    <span class="achievement-type"><?= escape($post['achievement_type']) ?></span>
                                    <?php if ($post['achievement_value']): ?>
                                        <span class="achievement-value"><?= escape($post['achievement_value']) ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php if ($post['content']): ?>
                                <p class="post-text"><?= nl2br(escape($post['content'])) ?></p>
                            <?php endif; ?>

                        <?php elseif ($post['post_type'] === 'media'): ?>
                            <?php if ($post['content']): ?>
                                <p class="post-text"><?= nl2br(escape($post['content'])) ?></p>
                            <?php endif; ?>
                            <?php if (!empty($post['media_urls'])): ?>
                                <div class="post-media-grid media-count-<?= min(count($post['media_urls']), 4) ?>">
                                    <?php foreach (array_slice($post['media_urls'], 0, 4) as $index => $url): ?>
                                        <div class="media-item">
                                            <?php if (strpos($url, '.mp4') !== false || strpos($url, '.webm') !== false): ?>
                                                <video src="<?= escape($url) ?>" controls></video>
                                            <?php else: ?>
                                                <img src="<?= escape($url) ?>" alt="Post media">
                                            <?php endif; ?>
                                            <?php if ($index === 3 && count($post['media_urls']) > 4): ?>
                                                <div class="media-more">+<?= count($post['media_urls']) - 4 ?></div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                        <?php elseif ($post['post_type'] === 'forum'): ?>
                            <?php if ($post['title']): ?>
                                <h5 class="post-title"><?= escape($post['title']) ?></h5>
                            <?php endif; ?>
                            <p class="post-text"><?= nl2br(escape($post['content'])) ?></p>

                        <?php elseif ($post['post_type'] === 'meetup'): ?>
                            <div class="meetup-card">
                                <h5 class="meetup-title">
                                    <i class="bi bi-calendar-event"></i>
                                    <?= escape($post['title']) ?>
                                </h5>
                                <div class="meetup-details">
                                    <div class="meetup-datetime">
                                        <i class="bi bi-clock"></i>
                                        <?= date('l, M j, Y \a\t g:i A', strtotime($post['meetup_datetime'])) ?>
                                    </div>
                                    <div class="meetup-location">
                                        <i class="bi bi-geo-alt"></i>
                                        <?= escape($post['meetup_location_name']) ?>
                                    </div>
                                </div>
                                <?php if ($post['meetup_latitude'] && $post['meetup_longitude']): ?>
                                    <div class="meetup-map" 
                                         id="map-<?= $post['id'] ?>" 
                                         data-lat="<?= $post['meetup_latitude'] ?>" 
                                         data-lng="<?= $post['meetup_longitude'] ?>"
                                         data-name="<?= escape($post['meetup_location_name']) ?>">
                                    </div>
                                <?php endif; ?>
                                <?php if ($post['content']): ?>
                                    <p class="post-text mt-3"><?= nl2br(escape($post['content'])) ?></p>
                                <?php endif; ?>
                                
                                <!-- RSVP Section -->
                                <div class="meetup-rsvp">
                                    <div class="rsvp-counts">
                                        <?php 
                                        $goingCount = count(array_filter($post['attendees'] ?? [], fn($a) => $a['status'] === 'going'));
                                        $interestedCount = count(array_filter($post['attendees'] ?? [], fn($a) => $a['status'] === 'interested'));
                                        ?>
                                        <span class="rsvp-count going"><i class="bi bi-check-circle-fill"></i> <?= $goingCount ?> going</span>
                                        <span class="rsvp-count interested"><i class="bi bi-star-fill"></i> <?= $interestedCount ?> interested</span>
                                    </div>
                                    <div class="rsvp-buttons">
                                        <button class="rsvp-btn <?= ($post['user_rsvp'] ?? '') === 'going' ? 'active' : '' ?>" 
                                                data-status="going" data-post-id="<?= $post['id'] ?>">
                                            <i class="bi bi-check-circle"></i> Going
                                        </button>
                                        <button class="rsvp-btn <?= ($post['user_rsvp'] ?? '') === 'interested' ? 'active' : '' ?>" 
                                                data-status="interested" data-post-id="<?= $post['id'] ?>">
                                            <i class="bi bi-star"></i> Interested
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Post Actions -->
                    <div class="post-actions">
                        <button class="action-btn like-btn <?= $post['user_liked'] ? 'liked' : '' ?>" data-post-id="<?= $post['id'] ?>">
                            <span class="fire-icon">🔥</span>
                            <span class="like-count"><?= $post['like_count'] ?></span>
                        </button>
                        <button class="action-btn comment-btn" data-post-id="<?= $post['id'] ?>">
                            <i class="bi bi-chat"></i>
                            <span><?= $post['comment_count'] ?></span>
                        </button>
                        <button class="action-btn share-btn">
                            <i class="bi bi-share"></i>
                        </button>
                    </div>

                    <!-- Comments Section -->
                    <div class="post-comments" id="comments-<?= $post['id'] ?>">
                        <?php foreach ($post['comments'] as $comment): ?>
                            <div class="comment">
                                <span class="comment-author"><?= escape($comment['user_name']) ?></span>
                                <span class="comment-text"><?= escape($comment['content']) ?></span>
                            </div>
                        <?php endforeach; ?>
                        <?php if ($post['comment_count'] > 3): ?>
                            <a href="#" class="view-more-comments">View all <?= $post['comment_count'] ?> comments</a>
                        <?php endif; ?>
                    </div>

                    <!-- Add Comment -->
                    <div class="add-comment">
                        <input type="text" class="comment-input" placeholder="Add a comment..." data-post-id="<?= $post['id'] ?>">
                        <button class="comment-submit" data-post-id="<?= $post['id'] ?>">
                            <i class="bi bi-send-fill"></i>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Create Post Modal -->
<div class="modal fade" id="createPostModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-plus-circle"></i> Create Post</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Post Type Selector -->
                <div class="post-type-selector mb-4">
                    <button class="type-option active" data-type="achievement">
                        <i class="bi bi-trophy-fill"></i>
                        <span>Achievement</span>
                    </button>
                    <button class="type-option" data-type="media">
                        <i class="bi bi-camera-fill"></i>
                        <span>Photo/Video</span>
                    </button>
                    <button class="type-option" data-type="forum">
                        <i class="bi bi-chat-square-text-fill"></i>
                        <span>Discussion</span>
                    </button>
                    <button class="type-option" data-type="meetup">
                        <i class="bi bi-geo-alt-fill"></i>
                        <span>Meetup</span>
                    </button>
                </div>

                <form id="createPostForm" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                    <input type="hidden" name="post_type" id="postType" value="achievement">

                    <!-- Achievement Fields -->
                    <div class="post-fields" id="fields-achievement">
                        <div class="mb-3">
                            <label class="form-label">Achievement Type</label>
                            <select class="form-select" name="achievement_type">
                                <option value="Personal Record">🏆 Personal Record (PR)</option>
                                <option value="Streak">🔥 Workout Streak</option>
                                <option value="Milestone">⭐ Milestone</option>
                                <option value="First Time">🎉 First Time</option>
                                <option value="Challenge Complete">💪 Challenge Complete</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Value (Optional)</label>
                            <input type="text" class="form-control" name="achievement_value" placeholder="e.g., 225 lbs, 30 days, 5K">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="content" rows="3" placeholder="Tell everyone about your achievement..."></textarea>
                        </div>
                    </div>

                    <!-- Media Fields -->
                    <div class="post-fields d-none" id="fields-media">
                        <div class="mb-3">
                            <label class="form-label">Photos/Videos</label>
                            <div class="media-upload-area" id="mediaUploadArea">
                                <i class="bi bi-cloud-arrow-up"></i>
                                <p>Drag & drop or click to upload</p>
                                <small>Max 10 files, 10MB images, 50MB videos</small>
                                <input type="file" name="media[]" id="mediaInput" multiple accept="image/*,video/*" class="d-none">
                            </div>
                            <div class="media-preview" id="mediaPreview"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Caption</label>
                            <textarea class="form-control" name="content" rows="2" placeholder="Write a caption..."></textarea>
                        </div>
                    </div>

                    <!-- Forum Fields -->
                    <div class="post-fields d-none" id="fields-forum">
                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" class="form-control" name="title" placeholder="What's on your mind?">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Content</label>
                            <textarea class="form-control" name="content" rows="5" placeholder="Share your thoughts, ask questions, start a discussion..."></textarea>
                        </div>
                    </div>

                    <!-- Meetup Fields -->
                    <div class="post-fields d-none" id="fields-meetup">
                        <div class="mb-3">
                            <label class="form-label">Event Title</label>
                            <input type="text" class="form-control" name="title" placeholder="e.g., Morning Leg Day Session">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Date & Time</label>
                                <input type="datetime-local" class="form-control" name="meetup_datetime">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Max Attendees (Optional)</label>
                                <input type="number" class="form-control" name="meetup_max_attendees" min="2" placeholder="Leave empty for unlimited">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Location</label>
                            <div class="position-relative">
                                <input type="text" class="form-control" id="meetupLocationSearch" name="meetup_location_name" placeholder="Search for a gym or location..." autocomplete="off">
                                <div id="meetup-location-suggestions" class="autocomplete-dropdown"></div>
                            </div>
                            <input type="hidden" name="meetup_latitude" id="meetupLatitude">
                            <input type="hidden" name="meetup_longitude" id="meetupLongitude">
                        </div>
                        <div class="mb-3">
                            <div id="meetupMapPicker" class="map-picker"></div>
                            <small class="text-muted">Click on the map to set location or search above</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description (Optional)</label>
                            <textarea class="form-control" name="content" rows="2" placeholder="What will you be doing? Any requirements?"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="submitPost">
                    <i class="bi bi-send-fill"></i> Post
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Post Modal -->
<div class="modal fade" id="editPostModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-pencil"></i> Edit Post</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editPostForm">
                    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                    <input type="hidden" name="post_id" id="editPostId">
                    
                    <div class="mb-3" id="editTitleGroup">
                        <label class="form-label">Title</label>
                        <input type="text" class="form-control" name="title" id="editPostTitle">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Content / Caption</label>
                        <textarea class="form-control" name="content" id="editPostContent" rows="4"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="submitEditPost">
                    <i class="bi bi-check-lg"></i> Save Changes
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deletePostModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger"><i class="bi bi-exclamation-triangle"></i> Delete Post</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this post? This action cannot be undone.</p>
                <input type="hidden" id="deletePostId">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeletePost">
                    <i class="bi bi-trash"></i> Delete
                </button>
            </div>
        </div>
    </div>
</div>

<style>
/* Feed Container */
.feed-container {
    max-width: 680px;
    margin: 0 auto;
    padding: 1rem 0;
}

/* Create Post Bar */
.create-post-bar {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 1rem;
}

.avatar-circle {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: var(--bg-darker);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: var(--primary);
}

.create-post-input {
    flex: 1;
    background: var(--bg-darker);
    border: 1px solid var(--border);
    border-radius: 24px;
    padding: 12px 20px;
    color: var(--text-muted);
    text-align: left;
    cursor: pointer;
    transition: all 0.3s ease;
}

.create-post-input:hover {
    border-color: var(--primary);
    color: var(--text);
}

.quick-post-types {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.quick-post-btn {
    background: var(--bg-darker);
    border: 1px solid var(--border);
    border-radius: 20px;
    padding: 6px 16px;
    color: var(--text-muted);
    font-size: 0.85rem;
    transition: all 0.3s ease;
}

.quick-post-btn:hover {
    border-color: var(--primary);
    color: var(--primary);
}

.quick-post-btn i {
    margin-right: 4px;
}

/* Feed Tabs */
.feed-tabs {
    display: flex;
    gap: 0.5rem;
    overflow-x: auto;
    padding-bottom: 0.5rem;
}

.feed-tab {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: 20px;
    color: var(--text-muted);
    font-size: 0.9rem;
    white-space: nowrap;
    transition: all 0.3s ease;
    text-decoration: none;
}

.feed-tab:hover {
    border-color: var(--primary);
    color: var(--primary);
}

.feed-tab.active {
    background: var(--primary);
    border-color: var(--primary);
    color: #000;
}

/* Post Card */
.post-card {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: 12px;
    margin-bottom: 1rem;
    overflow: hidden;
}

.post-header {
    display: flex;
    align-items: center;
    padding: 1rem;
    gap: 12px;
}

.post-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: var(--bg-darker);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary);
}

.post-user-info {
    flex: 1;
}

.post-username {
    font-weight: 600;
    color: var(--text);
    display: block;
}

.post-meta {
    font-size: 0.8rem;
    color: var(--text-muted);
}

.post-type-badge {
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.post-type-achievement { background: rgba(255, 193, 7, 0.2); color: #ffc107; }
.post-type-media { background: rgba(13, 202, 240, 0.2); color: #0dcaf0; }
.post-type-forum { background: rgba(111, 66, 193, 0.2); color: #6f42c1; }
.post-type-meetup { background: rgba(255, 68, 68, 0.2); color: var(--primary); }

/* Post Menu Dropdown */
.post-menu {
    margin-left: 8px;
}

.post-menu-btn {
    background: transparent;
    border: none;
    color: var(--text-muted);
    padding: 4px 8px;
    border-radius: 50%;
    transition: all 0.3s ease;
}

.post-menu-btn:hover {
    background: var(--bg-darker);
    color: var(--text);
}

.post-menu .dropdown-menu {
    background: var(--bg-card);
    border: 1px solid var(--border);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
}

.post-menu .dropdown-item {
    color: var(--text);
    padding: 8px 16px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.post-menu .dropdown-item:hover {
    background: var(--bg-darker);
    color: var(--primary);
}

.post-menu .dropdown-item.text-danger:hover {
    background: rgba(255, 68, 68, 0.1);
    color: #ff4444;
}
.post-type-forum { background: rgba(111, 66, 193, 0.2); color: #6f42c1; }
.post-type-meetup { background: rgba(255, 68, 68, 0.2); color: var(--primary); }

/* Post Content */
.post-content {
    padding: 0 1rem 1rem;
}

.post-title {
    color: var(--primary);
    margin-bottom: 0.5rem;
}

.post-text {
    color: var(--text);
    margin: 0;
    line-height: 1.5;
}

/* Achievement Banner */
.achievement-banner {
    background: linear-gradient(135deg, rgba(255, 193, 7, 0.1), rgba(255, 68, 68, 0.1));
    border: 1px solid rgba(255, 193, 7, 0.3);
    border-radius: 12px;
    padding: 1rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
}

.achievement-icon {
    font-size: 2.5rem;
    color: #ffc107;
    filter: drop-shadow(0 0 10px rgba(255, 193, 7, 0.5));
}

.achievement-type {
    display: block;
    font-weight: 600;
    color: var(--text);
}

.achievement-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--primary);
}

/* Media Grid */
.post-media-grid {
    display: grid;
    gap: 4px;
    border-radius: 12px;
    overflow: hidden;
    margin-top: 0.5rem;
}

.post-media-grid.media-count-1 { grid-template-columns: 1fr; }
.post-media-grid.media-count-2 { grid-template-columns: 1fr 1fr; }
.post-media-grid.media-count-3 { grid-template-columns: 1fr 1fr; grid-template-rows: 1fr 1fr; }
.post-media-grid.media-count-3 .media-item:first-child { grid-row: span 2; }
.post-media-grid.media-count-4 { grid-template-columns: 1fr 1fr; grid-template-rows: 1fr 1fr; }

.media-item {
    position: relative;
    aspect-ratio: 1;
    overflow: hidden;
}

.media-item img, .media-item video {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.media-more {
    position: absolute;
    inset: 0;
    background: rgba(0, 0, 0, 0.6);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    font-weight: 700;
    color: #fff;
}

/* Meetup Card */
.meetup-card {
    background: var(--bg-darker);
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 1rem;
}

.meetup-title {
    color: var(--primary);
    margin-bottom: 1rem;
}

.meetup-details {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    margin-bottom: 1rem;
}

.meetup-datetime, .meetup-location {
    display: flex;
    align-items: center;
    gap: 8px;
    color: var(--text-muted);
}

.meetup-datetime i, .meetup-location i {
    color: var(--primary);
}

.meetup-map {
    height: 200px;
    border-radius: 8px;
    overflow: hidden;
}

.meetup-rsvp {
    border-top: 1px solid var(--border);
    padding-top: 1rem;
    margin-top: 1rem;
}

.rsvp-counts {
    display: flex;
    gap: 1rem;
    margin-bottom: 0.75rem;
}

.rsvp-count {
    font-size: 0.85rem;
    color: var(--text-muted);
}

.rsvp-count.going { color: #4ade80; }
.rsvp-count.interested { color: #fbbf24; }

.rsvp-buttons {
    display: flex;
    gap: 0.5rem;
}

.rsvp-btn {
    flex: 1;
    padding: 8px 16px;
    border: 1px solid var(--border);
    border-radius: 8px;
    background: transparent;
    color: var(--text-muted);
    font-size: 0.85rem;
    transition: all 0.3s ease;
}

.rsvp-btn:hover {
    border-color: var(--primary);
    color: var(--primary);
}

.rsvp-btn.active {
    background: var(--primary);
    border-color: var(--primary);
    color: #000;
}

/* Post Actions */
.post-actions {
    display: flex;
    padding: 0.5rem 1rem;
    border-top: 1px solid var(--border);
    gap: 1rem;
}

.action-btn {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 8px 12px;
    border: none;
    background: transparent;
    color: var(--text-muted);
    font-size: 0.9rem;
    transition: all 0.3s ease;
    border-radius: 8px;
}

.action-btn:hover {
    background: rgba(255, 68, 68, 0.1);
    color: var(--primary);
}

.action-btn.liked {
    color: #ff6b35;
}

/* Fire Like Button Animation */
.like-btn {
    position: relative;
    overflow: visible;
}

.like-btn.liked {
    color: #ff6b35;
    text-shadow: 0 0 10px rgba(255, 107, 53, 0.8), 0 0 20px rgba(255, 68, 68, 0.6);
    animation: fireGlow 1.5s ease-in-out infinite alternate;
}

.like-btn.liked i {
    filter: drop-shadow(0 0 8px #ff6b35) drop-shadow(0 0 15px #ff4444);
}

@keyframes fireGlow {
    0% {
        text-shadow: 0 0 10px rgba(255, 107, 53, 0.8), 0 0 20px rgba(255, 68, 68, 0.6);
    }
    100% {
        text-shadow: 0 0 15px rgba(255, 193, 7, 0.9), 0 0 30px rgba(255, 107, 53, 0.8), 0 0 40px rgba(255, 68, 68, 0.6);
    }
}

/* Fire Pop Animation */
.like-btn.fire-pop {
    animation: firePop 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

@keyframes firePop {
    0% {
        transform: scale(1);
    }
    30% {
        transform: scale(1.5);
    }
    50% {
        transform: scale(0.9);
    }
    70% {
        transform: scale(1.2);
    }
    100% {
        transform: scale(1);
    }
}

/* Fire Icon */
.fire-icon {
    font-size: 1.1rem;
    filter: grayscale(1) opacity(0.6);
    transition: all 0.3s ease;
}

.like-btn.liked .fire-icon {
    filter: none;
    transform: scale(1.1);
}

/* Fire Particles */
.fire-particle {
    position: absolute;
    pointer-events: none;
    font-size: 1.2rem;
    animation: fireParticleRise 0.8s ease-out forwards;
    z-index: 10;
}

@keyframes fireParticleRise {
    0% {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
    100% {
        opacity: 0;
        transform: translateY(-50px) scale(0.5);
    }
}

/* Comments */
.post-comments {
    padding: 0 1rem 0.5rem;
}

.comment {
    margin-bottom: 6px;
    font-size: 0.9rem;
}

.comment-author {
    font-weight: 600;
    color: var(--text);
    margin-right: 6px;
}

.comment-text {
    color: var(--text-muted);
}

.view-more-comments {
    color: var(--text-muted);
    font-size: 0.85rem;
}

.add-comment {
    display: flex;
    padding: 0.75rem 1rem;
    border-top: 1px solid var(--border);
    gap: 8px;
}

.comment-input {
    flex: 1;
    background: transparent;
    border: none;
    color: var(--text);
    font-size: 0.9rem;
}

.comment-input:focus {
    outline: none;
}

.comment-input::placeholder {
    color: var(--text-muted);
}

.comment-submit {
    background: transparent;
    border: none;
    color: var(--primary);
    opacity: 0.5;
    transition: opacity 0.3s;
}

.comment-submit:hover {
    opacity: 1;
}

/* Empty Feed */
.empty-feed {
    text-align: center;
    padding: 4rem 2rem;
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: 12px;
}

.empty-icon {
    font-size: 4rem;
    color: var(--primary);
    margin-bottom: 1rem;
}

.empty-feed h4 {
    color: var(--primary);
}

.empty-feed p {
    color: var(--text-muted);
    margin-bottom: 1.5rem;
}

/* Modal Styles */
.modal-content {
    background: var(--bg-card);
    border: 1px solid var(--border);
}

.modal-header {
    border-bottom: 1px solid var(--border);
}

.modal-title {
    color: var(--primary);
}

.modal-footer {
    border-top: 1px solid var(--border);
}

/* Post Type Selector */
.post-type-selector {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 0.5rem;
}

.type-option {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 1rem 0.5rem;
    background: var(--bg-darker);
    border: 2px solid var(--border);
    border-radius: 12px;
    color: var(--text-muted);
    transition: all 0.3s ease;
    cursor: pointer;
}

.type-option i {
    font-size: 1.5rem;
    margin-bottom: 0.5rem;
}

.type-option span {
    font-size: 0.75rem;
    text-transform: uppercase;
}

.type-option:hover {
    border-color: var(--primary);
    color: var(--primary);
}

.type-option.active {
    border-color: var(--primary);
    background: rgba(255, 68, 68, 0.1);
    color: var(--primary);
    box-shadow: 0 0 20px rgba(255, 68, 68, 0.2);
}

/* Media Upload */
.media-upload-area {
    border: 2px dashed var(--border);
    border-radius: 12px;
    padding: 2rem;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.media-upload-area:hover {
    border-color: var(--primary);
}

.media-upload-area i {
    font-size: 3rem;
    color: var(--primary);
}

.media-upload-area p {
    color: var(--text-muted);
    margin: 0.5rem 0;
}

.media-preview {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-top: 1rem;
}

.media-preview-item {
    width: 80px;
    height: 80px;
    border-radius: 8px;
    overflow: hidden;
    position: relative;
}

.media-preview-item img, .media-preview-item video {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.media-preview-item .remove-media {
    position: absolute;
    top: 4px;
    right: 4px;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: rgba(0, 0, 0, 0.7);
    border: none;
    color: #fff;
    font-size: 0.7rem;
    cursor: pointer;
}

/* Map Picker */
.map-picker {
    height: 250px;
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid var(--border);
}

#meetup-location-suggestions {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-top: none;
    border-radius: 0 0 8px 8px;
    max-height: 200px;
    overflow-y: auto;
    z-index: 1050;
    display: none;
}

#meetup-location-suggestions.show {
    display: block;
}

@media (max-width: 768px) {
    .post-type-selector {
        grid-template-columns: repeat(2, 1fr);
    }
}
</style>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
const csrfToken = '<?= generateCsrfToken() ?>';

function debounce(func, wait) {
    let timeout;
    return function(...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), wait);
    };
}

document.addEventListener('DOMContentLoaded', () => {
    // Post type selector
    const typeOptions = document.querySelectorAll('.type-option');
    const postTypeInput = document.getElementById('postType');
    const fieldGroups = document.querySelectorAll('.post-fields');
    
    typeOptions.forEach(option => {
        option.addEventListener('click', () => {
            typeOptions.forEach(o => o.classList.remove('active'));
            option.classList.add('active');
            
            const type = option.dataset.type;
            postTypeInput.value = type;
            
            fieldGroups.forEach(group => group.classList.add('d-none'));
            document.getElementById(`fields-${type}`).classList.remove('d-none');
            
            if (type === 'meetup') setTimeout(initMeetupMap, 100);
        });
    });
    
    // Quick post buttons
    document.querySelectorAll('.quick-post-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const type = btn.dataset.type;
            document.querySelector(`.type-option[data-type="${type}"]`).click();
        });
    });
    
    // Media upload
    const mediaUploadArea = document.getElementById('mediaUploadArea');
    const mediaInput = document.getElementById('mediaInput');
    const mediaPreview = document.getElementById('mediaPreview');
    
    if (mediaUploadArea) {
        mediaUploadArea.addEventListener('click', () => mediaInput.click());
        mediaUploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            mediaUploadArea.style.borderColor = 'var(--primary)';
        });
        mediaUploadArea.addEventListener('dragleave', () => {
            mediaUploadArea.style.borderColor = 'var(--border)';
        });
        mediaUploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            mediaUploadArea.style.borderColor = 'var(--border)';
            handleMediaFiles(e.dataTransfer.files);
        });
        mediaInput.addEventListener('change', () => handleMediaFiles(mediaInput.files));
    }
    
    function handleMediaFiles(files) {
        mediaPreview.innerHTML = '';
        Array.from(files).slice(0, 10).forEach(file => {
            const item = document.createElement('div');
            item.className = 'media-preview-item';
            
            if (file.type.startsWith('image/')) {
                const img = document.createElement('img');
                img.src = URL.createObjectURL(file);
                item.appendChild(img);
            } else if (file.type.startsWith('video/')) {
                const video = document.createElement('video');
                video.src = URL.createObjectURL(file);
                item.appendChild(video);
            }
            
            const removeBtn = document.createElement('button');
            removeBtn.className = 'remove-media';
            removeBtn.innerHTML = '×';
            removeBtn.onclick = () => item.remove();
            item.appendChild(removeBtn);
            
            mediaPreview.appendChild(item);
        });
    }
    
    // Submit post
    document.getElementById('submitPost').addEventListener('click', async () => {
        const form = document.getElementById('createPostForm');
        const postType = document.getElementById('postType').value;
        const activeFields = document.getElementById(`fields-${postType}`);
        
        // Build FormData with only the active section's fields
        const formData = new FormData();
        formData.append('csrf_token', form.querySelector('[name="csrf_token"]').value);
        formData.append('post_type', postType);
        
        // Add fields from the active section only
        activeFields.querySelectorAll('input, textarea, select').forEach(field => {
            if (field.name) {
                if (field.type === 'file') {
                    for (const file of field.files) {
                        formData.append(field.name, file);
                    }
                } else {
                    formData.append(field.name, field.value);
                }
            }
        });
        
        try {
            const response = await fetch('/feed/post', { method: 'POST', body: formData });
            const data = await response.json();
            
            if (data.success) {
                window.location.reload();
            } else {
                alert(data.message || 'Failed to create post');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Failed to create post');
        }
    });
    
    // Like post with fire animation
    document.querySelectorAll('.like-btn').forEach(btn => {
        btn.addEventListener('click', async () => {
            const postId = btn.dataset.postId;
            const wasLiked = btn.classList.contains('liked');
            
            // Add pop animation
            btn.classList.add('fire-pop');
            setTimeout(() => btn.classList.remove('fire-pop'), 600);
            
            // Create fire particles when liking
            if (!wasLiked) {
                createFireParticles(btn);
            }
            
            try {
                const response = await fetch('/feed/like', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `csrf_token=${csrfToken}&post_id=${postId}`
                });
                
                const data = await response.json();
                
                if (data.success) {
                    btn.classList.toggle('liked', data.liked);
                    btn.querySelector('.like-count').textContent = data.like_count;
                }
            } catch (error) {
                console.error('Error:', error);
            }
        });
    });
    
    // Fire particles effect
    function createFireParticles(btn) {
        const particles = ['🔥', '✨', '💥', '⭐'];
        const rect = btn.getBoundingClientRect();
        
        for (let i = 0; i < 6; i++) {
            const particle = document.createElement('span');
            particle.className = 'fire-particle';
            particle.textContent = particles[Math.floor(Math.random() * particles.length)];
            particle.style.left = `${Math.random() * 40 - 10}px`;
            particle.style.top = `${Math.random() * 10 - 20}px`;
            particle.style.animationDelay = `${Math.random() * 0.2}s`;
            btn.appendChild(particle);
            
            setTimeout(() => particle.remove(), 1000);
        }
    }
    
    // Add comment
    document.querySelectorAll('.comment-submit').forEach(btn => {
        btn.addEventListener('click', async () => {
            const postId = btn.dataset.postId;
            const input = document.querySelector(`.comment-input[data-post-id="${postId}"]`);
            const content = input.value.trim();
            if (!content) return;
            
            try {
                const response = await fetch('/feed/comment', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `csrf_token=${csrfToken}&post_id=${postId}&content=${encodeURIComponent(content)}`
                });
                
                const data = await response.json();
                
                if (data.success) {
                    const commentsContainer = document.getElementById(`comments-${postId}`);
                    commentsContainer.insertAdjacentHTML('beforeend', `
                        <div class="comment">
                            <span class="comment-author">${data.comment.user_name}</span>
                            <span class="comment-text">${data.comment.content}</span>
                        </div>
                    `);
                    input.value = '';
                }
            } catch (error) {
                console.error('Error:', error);
            }
        });
    });
    
    // Enter to submit comment
    document.querySelectorAll('.comment-input').forEach(input => {
        input.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                document.querySelector(`.comment-submit[data-post-id="${input.dataset.postId}"]`).click();
            }
        });
    });
    
    // RSVP buttons
    document.querySelectorAll('.rsvp-btn').forEach(btn => {
        btn.addEventListener('click', async () => {
            const postId = btn.dataset.postId;
            const status = btn.dataset.status;
            
            try {
                const response = await fetch('/feed/rsvp', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `csrf_token=${csrfToken}&post_id=${postId}&status=${status}`
                });
                
                const data = await response.json();
                
                if (data.success) {
                    const card = btn.closest('.post-card');
                    card.querySelectorAll('.rsvp-btn').forEach(b => b.classList.remove('active'));
                    if (data.status !== 'not_going') btn.classList.add('active');
                    
                    card.querySelector('.rsvp-count.going').innerHTML = `<i class="bi bi-check-circle-fill"></i> ${data.going_count} going`;
                    card.querySelector('.rsvp-count.interested').innerHTML = `<i class="bi bi-star-fill"></i> ${data.interested_count} interested`;
                }
            } catch (error) {
                console.error('Error:', error);
            }
        });
    });
    
    // Initialize meetup maps
    document.querySelectorAll('.meetup-map').forEach(mapEl => {
        const lat = parseFloat(mapEl.dataset.lat);
        const lng = parseFloat(mapEl.dataset.lng);
        const name = mapEl.dataset.name;
        
        if (lat && lng) {
            const map = L.map(mapEl.id).setView([lat, lng], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap'
            }).addTo(map);
            L.marker([lat, lng]).addTo(map).bindPopup(name);
        }
    });
    
    // Meetup map picker
    let meetupMap = null;
    let meetupMarker = null;
    
    function initMeetupMap() {
        const mapContainer = document.getElementById('meetupMapPicker');
        if (!mapContainer || meetupMap) return;
        
        meetupMap = L.map('meetupMapPicker').setView([25.7617, -80.1918], 12);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap'
        }).addTo(meetupMap);
        
        meetupMap.on('click', (e) => {
            setMeetupLocation(e.latlng.lat, e.latlng.lng);
            reverseGeocode(e.latlng.lat, e.latlng.lng);
        });
    }
    
    function setMeetupLocation(lat, lng) {
        document.getElementById('meetupLatitude').value = lat;
        document.getElementById('meetupLongitude').value = lng;
        
        if (meetupMarker) {
            meetupMarker.setLatLng([lat, lng]);
        } else if (meetupMap) {
            meetupMarker = L.marker([lat, lng]).addTo(meetupMap);
        }
    }
    
    async function reverseGeocode(lat, lng) {
        try {
            const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`);
            const data = await response.json();
            if (data.display_name) {
                document.getElementById('meetupLocationSearch').value = data.display_name.split(',').slice(0, 3).join(',');
            }
        } catch (error) {
            console.error('Reverse geocode error:', error);
        }
    }
    
    // Location search
    const locationSearch = document.getElementById('meetupLocationSearch');
    const locationSuggestions = document.getElementById('meetup-location-suggestions');
    
    if (locationSearch) {
        locationSearch.addEventListener('input', debounce(async (e) => {
            const query = e.target.value.trim();
            if (query.length < 2) {
                locationSuggestions.classList.remove('show');
                return;
            }
            
            try {
                const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=5`);
                const results = await response.json();
                
                if (results.length > 0) {
                    locationSuggestions.innerHTML = results.map(r => `
                        <div class="autocomplete-item" data-lat="${r.lat}" data-lng="${r.lon}" data-name="${r.display_name}">
                            <div class="item-name"><i class="bi bi-geo-alt"></i> ${r.display_name.split(',').slice(0, 2).join(',')}</div>
                            <div class="item-address">${r.display_name.split(',').slice(2, 4).join(',')}</div>
                        </div>
                    `).join('');
                    
                    locationSuggestions.querySelectorAll('.autocomplete-item').forEach(item => {
                        item.addEventListener('click', () => {
                            const lat = parseFloat(item.dataset.lat);
                            const lng = parseFloat(item.dataset.lng);
                            locationSearch.value = item.dataset.name.split(',').slice(0, 3).join(',');
                            setMeetupLocation(lat, lng);
                            if (meetupMap) meetupMap.setView([lat, lng], 15);
                            locationSuggestions.classList.remove('show');
                        });
                    });
                    
                    locationSuggestions.classList.add('show');
                }
            } catch (error) {
                console.error('Location search error:', error);
            }
        }, 300));
        
        document.addEventListener('click', (e) => {
            if (!locationSearch.contains(e.target) && !locationSuggestions.contains(e.target)) {
                locationSuggestions.classList.remove('show');
            }
        });
    }
    
    document.getElementById('createPostModal').addEventListener('shown.bs.modal', () => {
        if (document.getElementById('postType').value === 'meetup') {
            setTimeout(initMeetupMap, 100);
        }
    });
    
    // Edit post handlers
    document.querySelectorAll('.edit-post-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const postId = btn.dataset.postId;
            const postType = btn.dataset.postType;
            const content = btn.dataset.content;
            const title = btn.dataset.title;
            
            document.getElementById('editPostId').value = postId;
            document.getElementById('editPostContent').value = content;
            document.getElementById('editPostTitle').value = title;
            
            // Show/hide title field based on post type
            const titleGroup = document.getElementById('editTitleGroup');
            if (postType === 'forum' || postType === 'meetup') {
                titleGroup.style.display = 'block';
            } else {
                titleGroup.style.display = 'none';
            }
            
            const editModal = new bootstrap.Modal(document.getElementById('editPostModal'));
            editModal.show();
        });
    });
    
    // Submit edit post
    document.getElementById('submitEditPost').addEventListener('click', async () => {
        const postId = document.getElementById('editPostId').value;
        const content = document.getElementById('editPostContent').value;
        const title = document.getElementById('editPostTitle').value;
        
        try {
            const response = await fetch('/feed/edit', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `csrf_token=${csrfToken}&post_id=${postId}&content=${encodeURIComponent(content)}&title=${encodeURIComponent(title)}`
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Update the post content in the DOM
                const postCard = document.querySelector(`.post-card[data-post-id="${postId}"]`);
                if (postCard) {
                    const postText = postCard.querySelector('.post-text');
                    if (postText) {
                        postText.innerHTML = content.replace(/\n/g, '<br>');
                    }
                    const postTitle = postCard.querySelector('.post-title, .meetup-title');
                    if (postTitle && title) {
                        postTitle.innerHTML = postTitle.querySelector('i') ? 
                            `<i class="bi bi-calendar-event"></i> ${title}` : title;
                    }
                    // Update the edit button data attributes
                    const editBtn = postCard.querySelector('.edit-post-btn');
                    if (editBtn) {
                        editBtn.dataset.content = content;
                        editBtn.dataset.title = title;
                    }
                }
                
                bootstrap.Modal.getInstance(document.getElementById('editPostModal')).hide();
            } else {
                alert(data.message || 'Failed to update post');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Failed to update post');
        }
    });
    
    // Delete post handlers
    document.querySelectorAll('.delete-post-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.getElementById('deletePostId').value = btn.dataset.postId;
            const deleteModal = new bootstrap.Modal(document.getElementById('deletePostModal'));
            deleteModal.show();
        });
    });
    
    // Confirm delete post
    document.getElementById('confirmDeletePost').addEventListener('click', async () => {
        const postId = document.getElementById('deletePostId').value;
        
        try {
            const response = await fetch('/feed/delete', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `csrf_token=${csrfToken}&post_id=${postId}`
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Remove the post from the DOM with animation
                const postCard = document.querySelector(`.post-card[data-post-id="${postId}"]`);
                if (postCard) {
                    postCard.style.transition = 'all 0.3s ease';
                    postCard.style.opacity = '0';
                    postCard.style.transform = 'scale(0.9)';
                    setTimeout(() => postCard.remove(), 300);
                }
                
                bootstrap.Modal.getInstance(document.getElementById('deletePostModal')).hide();
            } else {
                alert(data.message || 'Failed to delete post');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Failed to delete post');
        }
    });
});
</script>

<?php require __DIR__ . '/../partials/footer.php'; ?>
