<?php
$pageTitle = 'Find Partners - GymBro';
require __DIR__ . '/../partials/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <h2 class="text-center mb-4" style="color: var(--primary); font-size: 2.5rem;">
            <i class="bi bi-lightning-charge-fill"></i> FIND YOUR BRO
        </h2>

        <?php if ($candidate): ?>
            <?php
            $candidateWorkoutStyles = json_decode($candidate['workout_styles'], true) ?? [];
            ?>
            <div class="card shadow-lg card-profile" id="candidate-card">
                <div class="card-body p-4">
                    <div class="text-center mb-3">
                        <div class="display-1" style="color: var(--primary);">
                            <i class="bi bi-person-circle"></i>
                        </div>
                        <h3 class="mt-2" style="color: var(--primary);"><?= escape($candidate['name']) ?>, <?= escape($candidate['age']) ?></h3>
                        <p class="text-muted">
                            <?= escape($candidate['pronouns']) ?> • 
                            <?= escape(ucfirst(str_replace('_', ' ', $candidate['gender']))) ?>
                        </p>
                    </div>

                    <?php if ($candidate['short_bio']): ?>
                    <div class="mb-3">
                        <p class="text-center"><?= nl2br(escape($candidate['short_bio'])) ?></p>
                    </div>
                    <?php endif; ?>

                    <div class="mb-3 text-center">
                        <strong>Workout Styles:</strong><br>
                        <?php foreach ($candidateWorkoutStyles as $style): ?>
                            <span class="badge badge-workout me-1 mb-1">
                                <?= escape(ucfirst($style)) ?>
                            </span>
                        <?php endforeach; ?>
                    </div>

                    <div class="row text-center mb-3">
                        <?php if ($candidate['city']): ?>
                        <div class="col-6">
                            <p class="mb-0">
                                <i class="bi bi-geo-alt"></i><br>
                                <strong><?= escape($candidate['city']) ?></strong>
                            </p>
                        </div>
                        <?php endif; ?>
                        <?php if ($candidate['home_gym']): ?>
                        <div class="col-6">
                            <p class="mb-0">
                                <i class="bi bi-building"></i><br>
                                <strong><?= escape($candidate['home_gym']) ?></strong>
                            </p>
                        </div>
                        <?php endif; ?>
                    </div>

                    <?php if ($candidate['activity_level']): ?>
                    <div class="text-center mb-3">
                        <span class="badge bg-info">
                            <?= escape(ucwords(str_replace('_', ' ', $candidate['activity_level']))) ?>
                        </span>
                    </div>
                    <?php endif; ?>

                    <div class="d-flex gap-3 justify-content-center mt-4">
                        <button 
                            class="btn btn-outline-secondary btn-lg"
                            hx-post="/queue/pass"
                            hx-vals='{"csrf_token": "<?= generateCsrfToken() ?>", "passed_user_id": "<?= $candidate['id'] ?>"}'
                            hx-swap="none"
                            onclick="window.location.reload()"
                        >
                            <i class="bi bi-x-circle"></i> Pass
                        </button>
                        <button 
                            class="btn btn-primary btn-lg"
                            hx-post="/queue/like"
                            hx-vals='{"csrf_token": "<?= generateCsrfToken() ?>", "liked_user_id": "<?= $candidate['id'] ?>"}'
                            hx-swap="none"
                            hx-on::after-request="handleLikeResponse(event)"
                        >
                            <i class="bi bi-heart-fill"></i> Like
                        </button>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="card shadow-lg card-profile">
                <div class="card-body p-5 text-center">
                    <div class="display-1" style="color: var(--text-muted);">
                        <i class="bi bi-emoji-frown"></i>
                    </div>
                    <h4 class="mt-3" style="color: var(--primary);">No More Bros Right Now</h4>
                    <p class="text-muted">
                        We couldn't find anyone matching your preferences. 
                        Check back later or update your profile to expand your search!
                    </p>
                    <div class="mt-4">
                        <a href="/profile/edit" class="btn btn-primary me-2">
                            <i class="bi bi-pencil"></i> Edit Profile
                        </a>
                        <a href="/matches" class="btn btn-outline-primary">
                            <i class="bi bi-people"></i> View Matches
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function handleLikeResponse(event) {
    const response = JSON.parse(event.detail.xhr.response);
    if (response.success) {
        if (response.is_match) {
            showMatchPopup(response.matched_user_name);
        } else {
            window.location.reload();
        }
    } else {
        alert('Error: ' + response.message);
    }
}

function showMatchPopup(matchedUserName) {
    // Create overlay
    const overlay = document.createElement('div');
    overlay.className = 'match-overlay';
    overlay.innerHTML = `
        <div class="match-glow-lines">
            <div class="glow-line glow-line-1"></div>
            <div class="glow-line glow-line-2"></div>
            <div class="glow-line glow-line-3"></div>
            <div class="glow-line glow-line-4"></div>
            <div class="glow-line glow-line-5"></div>
            <div class="glow-line glow-line-6"></div>
        </div>
        <div class="match-popup">
            <div class="match-icon">
                <span class="flex-arm flex-left">💪</span>
                <span class="flex-arm flex-right">💪</span>
            </div>
            <h2 class="match-title">IT'S A MATCH!</h2>
            <p class="match-subtitle">You and <strong>${matchedUserName || 'your new bro'}</strong> are ready to crush it together</p>
            <div class="match-actions">
                <button class="btn btn-primary btn-lg" onclick="window.location.href='/matches'">
                    <i class="bi bi-chat-dots"></i> View Matches
                </button>
                <button class="btn btn-outline-primary btn-lg" onclick="closeMatchPopup()">
                    <i class="bi bi-arrow-right"></i> Keep Swiping
                </button>
            </div>
        </div>
    `;
    document.body.appendChild(overlay);
    
    // Trigger animation
    requestAnimationFrame(() => {
        overlay.classList.add('show');
    });
}

function closeMatchPopup() {
    const overlay = document.querySelector('.match-overlay');
    if (overlay) {
        overlay.classList.remove('show');
        overlay.classList.add('hide');
        setTimeout(() => {
            overlay.remove();
            window.location.reload();
        }, 300);
    }
}
</script>

<style>
.match-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.95);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    opacity: 0;
    transition: opacity 0.3s ease;
    overflow: hidden;
}

.match-overlay.show {
    opacity: 1;
}

.match-overlay.hide {
    opacity: 0;
}

.match-glow-lines {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
    overflow: hidden;
}

.glow-line {
    position: absolute;
    background: linear-gradient(90deg, 
        transparent, 
        rgba(255, 68, 68, 0.3), 
        rgba(255, 68, 68, 0.8), 
        rgba(255, 120, 120, 1), 
        rgba(255, 68, 68, 0.8), 
        rgba(255, 68, 68, 0.3), 
        transparent
    );
    height: 2px;
    width: 200%;
    left: -50%;
    filter: blur(1px);
    box-shadow: 
        0 0 10px rgba(255, 68, 68, 0.5),
        0 0 20px rgba(255, 68, 68, 0.3),
        0 0 40px rgba(255, 68, 68, 0.2);
}

.glow-line-1 {
    top: 15%;
    animation: glowMove 3s ease-in-out infinite;
    animation-delay: 0s;
}

.glow-line-2 {
    top: 30%;
    animation: glowMove 3.5s ease-in-out infinite reverse;
    animation-delay: 0.5s;
}

.glow-line-3 {
    top: 50%;
    animation: glowMove 2.5s ease-in-out infinite;
    animation-delay: 1s;
    height: 3px;
    background: linear-gradient(90deg, 
        transparent, 
        rgba(255, 68, 68, 0.4), 
        rgba(255, 68, 68, 1), 
        rgba(255, 150, 150, 1), 
        rgba(255, 68, 68, 1), 
        rgba(255, 68, 68, 0.4), 
        transparent
    );
}

.glow-line-4 {
    top: 65%;
    animation: glowMove 4s ease-in-out infinite reverse;
    animation-delay: 0.3s;
}

.glow-line-5 {
    top: 80%;
    animation: glowMove 3s ease-in-out infinite;
    animation-delay: 0.7s;
}

.glow-line-6 {
    top: 92%;
    animation: glowMove 3.2s ease-in-out infinite reverse;
    animation-delay: 1.2s;
}

@keyframes glowMove {
    0%, 100% {
        transform: translateX(-25%) rotate(-2deg);
        opacity: 0.6;
    }
    50% {
        transform: translateX(25%) rotate(2deg);
        opacity: 1;
    }
}

.match-popup {
    position: relative;
    z-index: 10;
    text-align: center;
    padding: 3rem;
    animation: popupEnter 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

@keyframes popupEnter {
    0% {
        transform: scale(0.5);
        opacity: 0;
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}

.match-icon {
    font-size: 5rem;
    margin-bottom: 1.5rem;
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.match-icon .flex-arm {
    filter: drop-shadow(0 0 20px rgba(255, 68, 68, 0.8));
    animation: flexPulse 1s ease-in-out infinite;
}

.match-icon .flex-left {
    transform: scaleX(-1);
    animation-delay: 0s;
}

.match-icon .flex-right {
    animation-delay: 0.5s;
}

@keyframes flexPulse {
    0%, 100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.2);
    }
}

.match-icon .flex-left {
    animation: flexPulseLeft 1s ease-in-out infinite;
}

@keyframes flexPulseLeft {
    0%, 100% {
        transform: scaleX(-1) scale(1);
    }
    50% {
        transform: scaleX(-1) scale(1.2);
    }
}

.match-title {
    font-family: 'Rajdhani', sans-serif;
    font-size: 4rem;
    font-weight: 700;
    color: var(--primary);
    text-shadow: 
        0 0 20px rgba(255, 68, 68, 0.5),
        0 0 40px rgba(255, 68, 68, 0.3),
        0 0 60px rgba(255, 68, 68, 0.2);
    margin-bottom: 1rem;
    letter-spacing: 3px;
    animation: titleGlow 2s ease-in-out infinite;
}

@keyframes titleGlow {
    0%, 100% {
        text-shadow: 
            0 0 20px rgba(255, 68, 68, 0.5),
            0 0 40px rgba(255, 68, 68, 0.3),
            0 0 60px rgba(255, 68, 68, 0.2);
    }
    50% {
        text-shadow: 
            0 0 30px rgba(255, 68, 68, 0.8),
            0 0 60px rgba(255, 68, 68, 0.5),
            0 0 90px rgba(255, 68, 68, 0.3);
    }
}

.match-subtitle {
    font-size: 1.3rem;
    color: var(--text);
    margin-bottom: 2.5rem;
}

.match-subtitle strong {
    color: var(--primary);
}

.match-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
}

.match-actions .btn {
    min-width: 180px;
}

@media (max-width: 576px) {
    .match-title {
        font-size: 2.5rem;
    }
    
    .match-icon {
        font-size: 3.5rem;
    }
    
    .match-actions {
        flex-direction: column;
        align-items: center;
    }
    
    .match-actions .btn {
        width: 100%;
        max-width: 280px;
    }
}
</style>

<?php require __DIR__ . '/../partials/footer.php'; ?>
