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
            alert('🎉 ' + response.message);
        }
        window.location.reload();
    } else {
        alert('Error: ' + response.message);
    }
}
</script>

<?php require __DIR__ . '/../partials/footer.php'; ?>
