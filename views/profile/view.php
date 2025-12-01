<?php
$pageTitle = 'My Profile - GymBro';
require __DIR__ . '/../partials/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-lg card-profile">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="mb-0" style="color: var(--primary);">
                        <i class="bi bi-person-circle"></i> <?= escape($user['name']) ?>
                    </h2>
                    <a href="/profile/edit" class="btn btn-outline-primary">
                        <i class="bi bi-pencil"></i> Edit Profile
                    </a>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="mb-2">
                            <strong>Age:</strong> <?= escape($user['age']) ?>
                        </p>
                        <p class="mb-2">
                            <strong>Pronouns:</strong> <?= escape($user['pronouns']) ?>
                        </p>
                        <p class="mb-2">
                            <strong>Gender:</strong> <?= escape(ucfirst(str_replace('_', ' ', $user['gender']))) ?>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-2">
                            <strong>Activity Level:</strong> 
                            <?php if ($user['activity_level']): ?>
                                <?= escape(ucwords(str_replace('_', ' ', $user['activity_level']))) ?>
                            <?php else: ?>
                                <span class="text-muted">Not set</span>
                            <?php endif; ?>
                        </p>
                        <p class="mb-2">
                            <strong>Email:</strong> <?= escape($user['email']) ?>
                        </p>
                    </div>
                </div>

                <div class="mb-3">
                    <strong>Workout Styles:</strong><br>
                    <?php foreach ($user['workout_styles'] as $style): ?>
                        <span class="badge badge-workout me-1 mb-1">
                            <?= escape(ucfirst($style)) ?>
                        </span>
                    <?php endforeach; ?>
                </div>

                <hr>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="mb-2">
                            <strong><i class="bi bi-geo-alt"></i> City:</strong> 
                            <?= $user['city'] ? escape($user['city']) : '<span class="text-muted">Not set</span>' ?>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-2">
                            <strong><i class="bi bi-building"></i> Home Gym:</strong> 
                            <?= $user['home_gym'] ? escape($user['home_gym']) : '<span class="text-muted">Not set</span>' ?>
                        </p>
                    </div>
                </div>

                <div class="mb-3">
                    <strong><i class="bi bi-card-text"></i> Bio:</strong>
                    <p class="mt-2">
                        <?= $user['short_bio'] ? nl2br(escape($user['short_bio'])) : '<span class="text-muted">No bio yet</span>' ?>
                    </p>
                </div>

                <?php if (!empty($user['preferred_partner_genders'])): ?>
                <div class="mb-3">
                    <strong><i class="bi bi-heart"></i> Looking for:</strong><br>
                    <?php foreach ($user['preferred_partner_genders'] as $pref): ?>
                        <span class="badge bg-secondary me-1">
                            <?= escape(ucfirst(str_replace('_', ' ', $pref))) ?>
                        </span>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <div class="text-muted small">
                    <i class="bi bi-calendar"></i> Member since <?= date('M d, Y', strtotime($user['created_at'])) ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
