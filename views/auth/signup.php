<?php
$pageTitle = 'Sign Up - GymBro';
require __DIR__ . '/../partials/header.php';

$errors = $_SESSION['signup_errors'] ?? [];
$oldInput = $_SESSION['old_input'] ?? [];
unset($_SESSION['signup_errors'], $_SESSION['old_input']);
?>

<div class="row justify-content-center">
    <div class="col-md-8 col-lg-7">
        <div class="card shadow-lg card-profile">
            <div class="card-body p-5">
                <h2 class="text-center mb-2" style="color: var(--primary); font-size: 2.5rem;">
                    <i class="bi bi-lightning-charge-fill"></i> JOIN GYMBRO
                </h2>
                <p class="text-center text-muted mb-4" style="text-transform: uppercase; letter-spacing: 1px; font-size: 0.85rem;">
                    Find Your Workout Partner
                </p>
                
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?= escape($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="POST" action="/signup" id="signupForm">
                    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Name *</label>
                            <input 
                                type="text" 
                                class="form-control" 
                                id="name" 
                                name="name" 
                                value="<?= escape($oldInput['name'] ?? '') ?>"
                                required
                            >
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="age" class="form-label">Age *</label>
                            <input 
                                type="number" 
                                class="form-control" 
                                id="age" 
                                name="age" 
                                min="13" 
                                max="100"
                                value="<?= escape($oldInput['age'] ?? '') ?>"
                                required
                            >
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="pronouns" class="form-label">Pronouns *</label>
                            <select class="form-select" id="pronouns" name="pronouns" required>
                                <option value="">Select...</option>
                                <option value="he/him" <?= ($oldInput['pronouns'] ?? '') === 'he/him' ? 'selected' : '' ?>>he/him</option>
                                <option value="she/her" <?= ($oldInput['pronouns'] ?? '') === 'she/her' ? 'selected' : '' ?>>she/her</option>
                                <option value="they/them" <?= ($oldInput['pronouns'] ?? '') === 'they/them' ? 'selected' : '' ?>>they/them</option>
                                <option value="prefer_not_to_say" <?= ($oldInput['pronouns'] ?? '') === 'prefer_not_to_say' ? 'selected' : '' ?>>Prefer not to say</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="gender" class="form-label">Gender *</label>
                            <select class="form-select" id="gender" name="gender" required>
                                <option value="">Select...</option>
                                <option value="male" <?= ($oldInput['gender'] ?? '') === 'male' ? 'selected' : '' ?>>Male</option>
                                <option value="female" <?= ($oldInput['gender'] ?? '') === 'female' ? 'selected' : '' ?>>Female</option>
                                <option value="nonbinary" <?= ($oldInput['gender'] ?? '') === 'nonbinary' ? 'selected' : '' ?>>Nonbinary</option>
                                <option value="prefer_not_to_say" <?= ($oldInput['gender'] ?? '') === 'prefer_not_to_say' ? 'selected' : '' ?>>Prefer not to say</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="activity_level" class="form-label">Activity Level (Optional)</label>
                        <select class="form-select" id="activity_level" name="activity_level">
                            <option value="">Select...</option>
                            <option value="not_very_active" <?= ($oldInput['activity_level'] ?? '') === 'not_very_active' ? 'selected' : '' ?>>Not Very Active</option>
                            <option value="kinda_active" <?= ($oldInput['activity_level'] ?? '') === 'kinda_active' ? 'selected' : '' ?>>Kinda Active</option>
                            <option value="super_gymbro" <?= ($oldInput['activity_level'] ?? '') === 'super_gymbro' ? 'selected' : '' ?>>Super GymBro</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Workout Styles * (Select at least one)</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="workout_styles[]" value="calisthenics" id="ws1" <?= in_array('calisthenics', $oldInput['workout_styles'] ?? []) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="ws1">Calisthenics</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="workout_styles[]" value="weightlifting" id="ws2" <?= in_array('weightlifting', $oldInput['workout_styles'] ?? []) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="ws2">Weightlifting</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="workout_styles[]" value="cardio" id="ws3" <?= in_array('cardio', $oldInput['workout_styles'] ?? []) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="ws3">Cardio</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="workout_styles[]" value="athletic" id="ws4" <?= in_array('athletic', $oldInput['workout_styles'] ?? []) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="ws4">Athletic</label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address *</label>
                        <input 
                            type="email" 
                            class="form-control" 
                            id="email" 
                            name="email" 
                            value="<?= escape($oldInput['email'] ?? '') ?>"
                            required
                        >
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password *</label>
                        <input 
                            type="password" 
                            class="form-control" 
                            id="password" 
                            name="password" 
                            pattern="^(?=.*[A-Z])(?=(?:.*\d){2,})(?=(?:.*[@#!?]){2,}).{8,}$"
                            title="Min 8 chars, 1 uppercase, 2 digits, 2 symbols from @#!?"
                            required
                        >
                        <div class="form-text">
                            Min 8 characters, 1 uppercase, 2 digits, 2 symbols from @#!?
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="password_confirm" class="form-label">Confirm Password *</label>
                        <input 
                            type="password" 
                            class="form-control" 
                            id="password_confirm" 
                            name="password_confirm" 
                            required
                        >
                    </div>

                    <button type="submit" class="btn btn-primary w-100 mb-3">
                        <i class="bi bi-person-plus"></i> Sign Up
                    </button>
                </form>

                <div class="text-center">
                    <p class="text-muted">Already have an account? <a href="/login">Login</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
