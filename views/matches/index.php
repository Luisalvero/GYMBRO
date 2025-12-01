<?php
$pageTitle = 'My Matches - GymBro';
require __DIR__ . '/../partials/header.php';
?>

<div class="row">
    <div class="col-12">
        <h2 class="mb-4" style="color: var(--primary);">
            <i class="bi bi-people-fill"></i> MY MATCHES
        </h2>

        <?php if (empty($matches)): ?>
            <div class="card shadow-lg card-profile">
                <div class="card-body p-5 text-center">
                    <div class="display-1" style="color: var(--text-muted);">
                        <i class="bi bi-lightning-charge"></i>
                    </div>
                    <h4 class="mt-3" style="color: var(--primary);">No Matches Yet</h4>
                    <p class="text-muted">
                        Start swiping to find your perfect workout partner!
                    </p>
                    <a href="/queue" class="btn btn-primary mt-3">
                        <i class="bi bi-lightning-charge-fill"></i> Find Bros
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($matches as $match): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card shadow-sm card-profile h-100">
                            <div class="card-body">
                                <div class="text-center mb-3">
                                    <div class="display-4" style="color: var(--primary);">
                                        <i class="bi bi-person-circle"></i>
                                    </div>
                                    <h5 class="mt-2" style="color: var(--primary);"><?= escape($match['name']) ?>, <?= escape($match['age']) ?></h5>
                                    <p class="text-muted small">
                                        <?= escape($match['pronouns']) ?> • 
                                        <?= escape(ucfirst(str_replace('_', ' ', $match['gender']))) ?>
                                    </p>
                                </div>

                                <?php if ($match['short_bio']): ?>
                                <p class="small text-center mb-3">
                                    <?= escape(substr($match['short_bio'], 0, 100)) ?>
                                    <?= strlen($match['short_bio']) > 100 ? '...' : '' ?>
                                </p>
                                <?php endif; ?>

                                <div class="mb-3">
                                    <?php foreach ($match['workout_styles'] as $style): ?>
                                        <span class="badge badge-workout me-1 mb-1">
                                            <?= escape(ucfirst($style)) ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>

                                <?php if ($match['city'] || $match['home_gym']): ?>
                                <hr>
                                <div class="small">
                                    <?php if ($match['city']): ?>
                                        <p class="mb-1">
                                            <i class="bi bi-geo-alt"></i> <?= escape($match['city']) ?>
                                        </p>
                                    <?php endif; ?>
                                    <?php if ($match['home_gym']): ?>
                                        <p class="mb-1">
                                            <i class="bi bi-building"></i> <?= escape($match['home_gym']) ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                                <?php endif; ?>

                                <div class="text-center mt-3">
                                    <small class="text-muted">
                                        <i class="bi bi-lightning-charge-fill" style="color: var(--primary);"></i> 
                                        Matched <?= date('M d, Y', strtotime($match['created_at'])) ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
