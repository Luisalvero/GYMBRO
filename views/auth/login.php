<?php
$pageTitle = 'Login - GymBro';
require __DIR__ . '/../partials/header.php';

$errors = $_SESSION['login_errors'] ?? [];
$oldEmail = $_SESSION['old_email'] ?? '';
unset($_SESSION['login_errors'], $_SESSION['old_email']);
?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow-lg card-profile">
            <div class="card-body p-5">
                <h2 class="text-center mb-2" style="color: var(--primary); font-size: 2.5rem;">
                    <i class="bi bi-lightning-charge-fill"></i> GYMBRO
                </h2>
                <p class="text-center text-muted mb-4" style="text-transform: uppercase; letter-spacing: 1px; font-size: 0.85rem;">
                    Time to Get Stronger
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

                <form method="POST" action="/login">
                    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input 
                            type="email" 
                            class="form-control" 
                            id="email" 
                            name="email" 
                            value="<?= escape($oldEmail) ?>"
                            required
                        >
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input 
                            type="password" 
                            class="form-control" 
                            id="password" 
                            name="password" 
                            required
                        >
                    </div>

                    <button type="submit" class="btn btn-primary w-100 mb-3">
                        <i class="bi bi-box-arrow-in-right"></i> Login
                    </button>
                </form>

                <div class="text-center">
                    <p class="text-muted">Don't have an account? <a href="/signup">Sign up</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
