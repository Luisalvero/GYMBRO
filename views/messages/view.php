<?php
/**
 * Chat View - Individual Conversation
 */
$pageTitle = 'Chat with ' . escape($conversation['other_user_name']) . ' - GymBro';
require __DIR__ . '/../partials/header.php';

$currentUserId = getCurrentUserId();
?>

<style>
    .chat-container {
        display: flex;
        flex-direction: column;
        height: calc(100vh - 180px);
        max-height: 700px;
    }
    
    .chat-header {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 1rem 1.25rem;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    
    .chat-avatar {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary) 0%, #dd3636 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        font-weight: 700;
        color: #000;
        flex-shrink: 0;
    }
    
    .chat-name {
        font-weight: 600;
        font-size: 1.1rem;
        color: var(--text);
    }
    
    .messages-container {
        flex: 1;
        overflow-y: auto;
        padding: 1rem;
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 12px;
        margin-bottom: 1rem;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }
    
    .message {
        max-width: 75%;
        padding: 0.75rem 1rem;
        border-radius: 18px;
        position: relative;
        word-wrap: break-word;
    }
    
    .message-sent {
        background: linear-gradient(135deg, var(--primary) 0%, #dd3636 100%);
        color: #000;
        align-self: flex-end;
        border-bottom-right-radius: 4px;
    }
    
    .message-received {
        background: var(--bg-darker);
        color: var(--text);
        align-self: flex-start;
        border-bottom-left-radius: 4px;
        border: 1px solid var(--border);
    }
    
    .message-content {
        margin-bottom: 0.25rem;
        line-height: 1.4;
    }
    
    .message-time {
        font-size: 0.7rem;
        opacity: 0.7;
        text-align: right;
    }
    
    .message-sent .message-time {
        color: rgba(0, 0, 0, 0.6);
    }
    
    .chat-input-container {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 1rem;
    }
    
    .chat-input-form {
        display: flex;
        gap: 0.75rem;
    }
    
    .chat-input {
        flex: 1;
        background: var(--bg-darker);
        border: 1px solid var(--border);
        border-radius: 25px;
        padding: 0.75rem 1.25rem;
        color: var(--text);
        font-size: 0.95rem;
        transition: all 0.3s ease;
    }
    
    .chat-input:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(255, 68, 68, 0.1);
    }
    
    .chat-input::placeholder {
        color: var(--text-muted);
    }
    
    .send-btn {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary) 0%, #dd3636 100%);
        border: none;
        color: #000;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        flex-shrink: 0;
    }
    
    .send-btn:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 15px rgba(255, 68, 68, 0.4);
    }
    
    .send-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        transform: none;
    }
    
    .empty-chat {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--text-muted);
        text-align: center;
    }
    
    .empty-chat i {
        font-size: 3rem;
        opacity: 0.3;
        margin-bottom: 1rem;
    }
    
    .typing-indicator {
        display: none;
        padding: 0.5rem 1rem;
        background: var(--bg-darker);
        border-radius: 18px;
        align-self: flex-start;
        color: var(--text-muted);
        font-size: 0.85rem;
    }
    
    .input-wrapper {
        flex: 1;
        position: relative;
    }
    
    .input-wrapper .chat-input {
        width: 100%;
        padding-right: 70px;
    }
    
    .char-counter {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 0.75rem;
        color: var(--text-muted);
        transition: color 0.3s ease;
    }
    
    .char-counter.warning {
        color: #ffa500;
    }
    
    .char-counter.danger {
        color: var(--primary);
        font-weight: 600;
    }
</style>

<div class="row justify-content-center mt-4">
    <div class="col-12 col-md-8 col-lg-6">
        
        <div class="chat-header">
            <a href="/messages" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div class="chat-avatar">
                <?= strtoupper(substr($conversation['other_user_name'], 0, 1)) ?>
            </div>
            <div class="chat-name"><?= escape($conversation['other_user_name']) ?></div>
        </div>
        
        <div class="chat-container">
            <div class="messages-container" id="messagesContainer">
                <?php if (empty($messages)): ?>
                    <div class="empty-chat" id="emptyChat">
                        <div>
                            <i class="bi bi-chat-heart d-block"></i>
                            <p>Say hi to your gym partner! 💪</p>
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($messages as $msg): ?>
                        <div class="message <?= $msg['sender_id'] == $currentUserId ? 'message-sent' : 'message-received' ?>" 
                             data-message-id="<?= $msg['id'] ?>">
                            <div class="message-content"><?= nl2br(escape($msg['content'])) ?></div>
                            <div class="message-time">
                                <?= date('g:i A', strtotime($msg['created_at'])) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <div class="chat-input-container">
                <form class="chat-input-form" id="messageForm" onsubmit="sendMessage(event)">
                    <div class="input-wrapper">
                        <input type="text" 
                               class="chat-input" 
                               id="messageInput" 
                               placeholder="Type a message..." 
                               autocomplete="off"
                               maxlength="500"
                               required>
                        <span class="char-counter" id="charCounter">0/500</span>
                    </div>
                    <button type="submit" class="send-btn" id="sendBtn">
                        <i class="bi bi-send-fill"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
const conversationId = <?= $conversationId ?>;
const currentUserId = <?= $currentUserId ?>;
let lastMessageId = <?= !empty($messages) ? end($messages)['id'] : 0 ?>;
let isPolling = true;
let pollInterval = null;
const MAX_CHARS = 500;

const messagesContainer = document.getElementById('messagesContainer');
const messageInput = document.getElementById('messageInput');
const sendBtn = document.getElementById('sendBtn');
const emptyChat = document.getElementById('emptyChat');
const charCounter = document.getElementById('charCounter');

// Character counter
messageInput.addEventListener('input', function() {
    const len = this.value.length;
    charCounter.textContent = `${len}/${MAX_CHARS}`;
    
    charCounter.classList.remove('warning', 'danger');
    if (len >= MAX_CHARS) {
        charCounter.classList.add('danger');
    } else if (len >= MAX_CHARS * 0.8) {
        charCounter.classList.add('warning');
    }
});

// Scroll to bottom on load
scrollToBottom();

function scrollToBottom() {
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
}

function formatTime(dateStr) {
    const date = new Date(dateStr);
    return date.toLocaleTimeString([], { hour: 'numeric', minute: '2-digit' });
}

function addMessage(msg, isSent) {
    // Remove empty chat placeholder if exists
    if (emptyChat) {
        emptyChat.remove();
    }
    
    const messageDiv = document.createElement('div');
    messageDiv.className = `message ${isSent ? 'message-sent' : 'message-received'}`;
    messageDiv.setAttribute('data-message-id', msg.id);
    
    messageDiv.innerHTML = `
        <div class="message-content">${escapeHtml(msg.content).replace(/\n/g, '<br>')}</div>
        <div class="message-time">${formatTime(msg.created_at)}</div>
    `;
    
    messagesContainer.appendChild(messageDiv);
    scrollToBottom();
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

async function sendMessage(event) {
    event.preventDefault();
    
    const content = messageInput.value.trim();
    if (!content) return;
    
    // Disable input while sending
    sendBtn.disabled = true;
    messageInput.disabled = true;
    
    try {
        const formData = new FormData();
        formData.append('conversation_id', conversationId);
        formData.append('content', content);
        
        const response = await fetch('/messages/send', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            addMessage(data.message, true);
            messageInput.value = '';
            lastMessageId = data.message.id;
        } else {
            alert(data.error || 'Failed to send message');
        }
    } catch (error) {
        console.error('Send error:', error);
        alert('Failed to send message. Please try again.');
    } finally {
        sendBtn.disabled = false;
        messageInput.disabled = false;
        messageInput.focus();
    }
}

async function pollMessages() {
    if (!isPolling) return;
    
    try {
        const response = await fetch(`/messages/fetch?conversation_id=${conversationId}&last_message_id=${lastMessageId}`);
        const data = await response.json();
        
        if (data.success && data.messages.length > 0) {
            data.messages.forEach(msg => {
                // Only add if not already displayed
                if (!document.querySelector(`[data-message-id="${msg.id}"]`)) {
                    addMessage(msg, msg.sender_id == currentUserId);
                    lastMessageId = msg.id;
                }
            });
        }
    } catch (error) {
        console.error('Poll error:', error);
    }
}

// Start polling every 2 seconds
pollInterval = setInterval(pollMessages, 2000);

// Clean up on page leave
window.addEventListener('beforeunload', () => {
    isPolling = false;
    if (pollInterval) clearInterval(pollInterval);
});

// Focus input on load
messageInput.focus();
</script>

<?php require __DIR__ . '/../partials/footer.php'; ?>
