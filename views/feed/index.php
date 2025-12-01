<?php
$pageTitle = 'Feed - GymBro';
require __DIR__ . '/../partials/header.php';
?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 style="color: var(--primary);">
                <i class="bi bi-house-door-fill"></i> FEED
            </h2>
        </div>

        <div class="card shadow-lg card-profile mb-4">
            <div class="card-body p-4">
                <div class="text-center py-5">
                    <div class="display-1" style="color: var(--primary);">
                        <i class="bi bi-lightning-charge-fill"></i>
                    </div>
                    <h4 class="mt-3" style="color: var(--primary);">Welcome to GymBro!</h4>
                    <p class="text-muted">
                        Your feed is currently empty. Start by finding workout partners and building your network!
                    </p>
                    <div class="mt-4">
                        <a href="/queue" class="btn btn-primary me-2">
                            <i class="bi bi-lightning-charge-fill"></i> Find Bros
                        </a>
                        <a href="/matches" class="btn btn-outline-primary">
                            <i class="bi bi-people"></i> View Matches
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Placeholder for future posts -->
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> 
            <strong>Coming Soon:</strong> Post updates, share workout achievements, and interact with your gym buddies!
        </div>
    </div>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
