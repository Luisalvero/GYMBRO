<?php
/**
 * Messages List View - Conversation List
 */
$pageTitle = 'Messages - GymBro';
require __DIR__ . '/../partials/header.php';
?>

<style>
    .conversation-item {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 1rem 1.25rem;
        margin-bottom: 0.75rem;
        transition: all 0.3s ease;
        text-decoration: none;
        display: block;
    }
    
    .conversation-item:hover {
        border-color: var(--primary);
        transform: translateX(5px);
        box-shadow: 0 4px 15px rgba(255, 68, 68, 0.15);
    }
    
    .conversation-item.has-unread {
        border-left: 3px solid var(--primary);
    }
    
    .conversation-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary) 0%, #dd3636 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        font-weight: 700;
        color: #000;
        flex-shrink: 0;
    }
    
    .conversation-content {
        flex: 1;
        min-width: 0;
    }
    
    .conversation-name {
        font-weight: 600;
        color: var(--text);
        font-size: 1.05rem;
        margin-bottom: 0.25rem;
    }
    
    .conversation-preview {
        color: var(--text-muted);
        font-size: 0.9rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .conversation-meta {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 0.25rem;
    }
    
    .conversation-time {
        color: var(--text-muted);
        font-size: 0.75rem;
    }
    
    .unread-badge {
        background: var(--primary);
        color: #000;
        font-size: 0.7rem;
        font-weight: 700;
        padding: 0.2rem 0.5rem;
        border-radius: 10px;
        min-width: 20px;
        text-align: center;
    }
    
    .empty-messages {
        text-align: center;
        padding: 4rem 2rem;
        color: var(--text-muted);
    }
    
    .empty-messages i {
        font-size: 4rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }
</style>

<div class="row justify-content-center mt-4">
    <div class="col-12 col-md-8 col-lg-6">
        <div class="d-flex align-items-center mb-4">
            <h2 class="mb-0">
                <i class="bi bi-chat-dots text-primary me-2"></i>Messages
            </h2>
        </div>
        
        <?php if (empty($conversations)): ?>
            <div class="empty-messages">
                <i class="bi bi-chat-square-text"></i>
                <h4>No conversations yet</h4>
                <p class="text-muted mb-3">Match with gym partners to start messaging!</p>
                <a href="/queue" class="btn btn-primary">
                    <i class="bi bi-fire me-1"></i> Find Partners
                </a>
            </div>
        <?php else: ?>
            <?php foreach ($conversations as $conv): ?>
                <a href="/messages/view?id=<?= $conv['conversation_id'] ?>" 
                   class="conversation-item d-flex align-items-center gap-3 <?= $conv['unread_count'] > 0 ? 'has-unread' : '' ?>">
                    <div class="conversation-avatar">
                        <?= strtoupper(substr($conv['other_user_name'], 0, 1)) ?>
                    </div>
                    <div class="conversation-content">
                        <div class="conversation-name"><?= escape($conv['other_user_name']) ?></div>
                        <div class="conversation-preview">
                            <?php if ($conv['last_message']): ?>
                                <?= escape(mb_substr($conv['last_message'], 0, 50)) ?><?= mb_strlen($conv['last_message']) > 50 ? '...' : '' ?>
                            <?php else: ?>
                                <em>Start a conversation!</em>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="conversation-meta">
                        <?php if ($conv['last_message_time']): ?>
                            <span class="conversation-time">
                                <?= timeAgo($conv['last_message_time']) ?>
                            </span>
                        <?php endif; ?>
                        <?php if ($conv['unread_count'] > 0): ?>
                            <span class="unread-badge"><?= $conv['unread_count'] > 99 ? '99+' : $conv['unread_count'] ?></span>
                        <?php endif; ?>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
