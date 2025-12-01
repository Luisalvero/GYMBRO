<?php
$pageTitle = 'Edit Profile - GymBro';
require __DIR__ . '/../partials/header.php';

$errors = $_SESSION['profile_errors'] ?? [];
unset($_SESSION['profile_errors']);
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-lg card-profile">
            <div class="card-body p-4">
                <h2 class="mb-4" style="color: var(--primary);">
                    <i class="bi bi-pencil"></i> Edit Profile
                </h2>
                
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?= escape($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="POST" action="/profile/update">
                    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                    
                    <div class="mb-3">
                        <label for="short_bio" class="form-label">Bio (Optional)</label>
                        <textarea 
                            class="form-control" 
                            id="short_bio" 
                            name="short_bio" 
                            rows="4"
                            maxlength="500"
                            placeholder="Tell others about yourself, your fitness goals, what you're looking for in a workout partner..."
                        ><?= escape($user['short_bio'] ?? '') ?></textarea>
                        <div class="form-text">Max 500 characters</div>
                    </div>

                    <div class="mb-3">
                        <label for="home_gym" class="form-label">Home Gym (Optional)</label>
                        <input 
                            type="text" 
                            class="form-control" 
                            id="home_gym" 
                            name="home_gym" 
                            value="<?= escape($user['home_gym'] ?? '') ?>"
                            placeholder="e.g., Gold's Gym Downtown, Planet Fitness"
                            maxlength="255"
                        >
                    </div>

                    <div class="mb-3">
                        <label for="city" class="form-label">City (Optional)</label>
                        <input 
                            type="text" 
                            class="form-control" 
                            id="city" 
                            name="city" 
                            value="<?= escape($user['city'] ?? '') ?>"
                            placeholder="e.g., San Francisco, CA"
                            maxlength="100"
                        >
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Partner Gender Preferences (Optional)</label>
                        <div class="form-text mb-2">Leave empty to see all genders</div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="preferred_partner_genders[]" value="male" id="pg1" <?= in_array('male', $user['preferred_partner_genders']) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="pg1">Male</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="preferred_partner_genders[]" value="female" id="pg2" <?= in_array('female', $user['preferred_partner_genders']) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="pg2">Female</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="preferred_partner_genders[]" value="nonbinary" id="pg3" <?= in_array('nonbinary', $user['preferred_partner_genders']) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="pg3">Nonbinary</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="preferred_partner_genders[]" value="prefer_not_to_say" id="pg4" <?= in_array('prefer_not_to_say', $user['preferred_partner_genders']) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="pg4">Prefer not to say</label>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Save Changes
                        </button>
                        <a href="/profile" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
