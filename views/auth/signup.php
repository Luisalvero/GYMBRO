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
                        <div class="workout-boxes">
                            <label class="workout-box <?= in_array('calisthenics', $oldInput['workout_styles'] ?? []) ? 'selected' : '' ?>">
                                <input type="checkbox" name="workout_styles[]" value="calisthenics" <?= in_array('calisthenics', $oldInput['workout_styles'] ?? []) ? 'checked' : '' ?>>
                                <i class="bi bi-person-arms-up"></i>
                                <span>Calisthenics</span>
                            </label>
                            <label class="workout-box <?= in_array('weightlifting', $oldInput['workout_styles'] ?? []) ? 'selected' : '' ?>">
                                <input type="checkbox" name="workout_styles[]" value="weightlifting" <?= in_array('weightlifting', $oldInput['workout_styles'] ?? []) ? 'checked' : '' ?>>
                                <i class="bi bi-trophy"></i>
                                <span>Weightlifting</span>
                            </label>
                            <label class="workout-box <?= in_array('cardio', $oldInput['workout_styles'] ?? []) ? 'selected' : '' ?>">
                                <input type="checkbox" name="workout_styles[]" value="cardio" <?= in_array('cardio', $oldInput['workout_styles'] ?? []) ? 'checked' : '' ?>>
                                <i class="bi bi-heart-pulse"></i>
                                <span>Cardio</span>
                            </label>
                            <label class="workout-box <?= in_array('athletic', $oldInput['workout_styles'] ?? []) ? 'selected' : '' ?>">
                                <input type="checkbox" name="workout_styles[]" value="athletic" <?= in_array('athletic', $oldInput['workout_styles'] ?? []) ? 'checked' : '' ?>>
                                <i class="bi bi-lightning-charge"></i>
                                <span>Athletic</span>
                            </label>
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

<style>
/* Workout style boxes */
.workout-boxes {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1rem;
}

@media (max-width: 768px) {
    .workout-boxes {
        grid-template-columns: repeat(2, 1fr);
    }
}

.workout-box {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 1.5rem 1rem;
    background: var(--bg-darker);
    border: 2px solid var(--border);
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
    text-align: center;
}

.workout-box input[type="checkbox"] {
    display: none;
}

.workout-box i {
    font-size: 2rem;
    margin-bottom: 0.5rem;
    color: var(--text-muted);
    transition: all 0.3s ease;
}

.workout-box span {
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.85rem;
    letter-spacing: 0.5px;
    color: var(--text-muted);
    transition: all 0.3s ease;
}

.workout-box:hover {
    border-color: var(--primary);
    background: rgba(255, 68, 68, 0.05);
}

.workout-box:hover i,
.workout-box:hover span {
    color: var(--primary);
}

.workout-box.selected {
    border-color: var(--primary);
    background: rgba(255, 68, 68, 0.1);
    box-shadow: 
        0 0 20px rgba(255, 68, 68, 0.3),
        0 0 40px rgba(255, 68, 68, 0.2),
        inset 0 0 20px rgba(255, 68, 68, 0.1);
}

.workout-box.selected i {
    color: var(--primary);
    filter: drop-shadow(0 0 8px rgba(255, 68, 68, 0.8));
}

.workout-box.selected span {
    color: var(--primary);
    text-shadow: 0 0 10px rgba(255, 68, 68, 0.5);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Workout box toggle
    document.querySelectorAll('.workout-box').forEach(box => {
        box.addEventListener('click', function() {
            const checkbox = this.querySelector('input[type="checkbox"]');
            checkbox.checked = !checkbox.checked;
            this.classList.toggle('selected', checkbox.checked);
        });
    });
});
</script>

<?php require __DIR__ . '/../partials/footer.php'; ?>
