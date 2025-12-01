<?php
$pageTitle = '404 Not Found - GymBro';
require __DIR__ . '/../partials/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6 text-center">
        <div class="display-1" style="color: var(--danger);">
            <i class="bi bi-exclamation-triangle"></i>
        </div>
        <h2 class="mt-3" style="color: var(--primary);">404 - PAGE NOT FOUND</h2>
        <p class="text-muted">
            The page you're looking for doesn't exist.
        </p>
        <a href="/" class="btn btn-primary mt-3">
            <i class="bi bi-house"></i> Go Home
        </a>
    </div>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
