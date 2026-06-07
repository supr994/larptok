<?php
require_once 'includes/auth.php';
include 'includes/header.php';
?>

<div class="container text-white mt-3">


    <div class="mb-4">
        <img src="/assets/img/banner.jpg"
             class="w-100 rounded"
             style="max-height:220px; object-fit:cover;">
    </div>

    <div class="p-3 bg-dark rounded border border-secondary">

        <h4 class="fw-bold mb-1">Welcome to LarpTok</h4>

        <p class="text-muted mb-3">
            Watch and upload videos for FREE!
        </p>
<hr>
        <?php if (loggedIn()): ?>

            <div class="d-flex gap-2">
                <a href="/foryou.php" class="btn btn-primary">
                    For You
                </a>

                <a href="/upload.php" class="btn btn-outline-light">
                    Upload
                </a>
            </div>

        <?php else: ?>

            <div class="d-flex gap-2">
                <a href="/login.php" class="btn btn-primary">Login</a>
                <a href="/register.php" class="btn btn-outline-light">Register</a>
            </div>

        <?php endif; ?>

    </div>

</div>

<?php include 'includes/footer.php'; ?>