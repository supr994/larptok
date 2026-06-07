<?php

require_once 'includes/db.php';
require_once 'includes/auth.php';

include 'includes/header.php';

$query = trim($_GET['q'] ?? '');
$results = [];

if ($query !== '') {

    $stmt = $pdo->prepare("
        SELECT id, username, display_name, avatar, verified
        FROM users
        WHERE username LIKE ? OR display_name LIKE ?
        LIMIT 20
    ");

    $like = "%$query%";
    $stmt->execute([$like, $like]);

    $results = $stmt->fetchAll();
}

?>

<div class="container text-white">

    <h2>Search</h2>
    <hr>

    <form method="GET" action="/search.php" class="mb-3">

        <input type="text"
               name="q"
               class="form-control"
               placeholder="Search"
               value="<?= htmlspecialchars($query) ?>">

    </form>

    <?php if ($query === ''): ?>

        <p class="text-muted">Type something to search.</p>

    <?php elseif (empty($results)): ?>

        <p class="text-muted">uh oh. Nothing to see here!</p>

    <?php else: ?>

        <?php foreach ($results as $user): ?>

            <a href="/profile.php?user=<?= urlencode($user['username']) ?>"
               class="d-flex align-items-center gap-2 text-white text-decoration-none mb-3">

                <img src="/<?= htmlspecialchars($user['avatar'] ?? 'uploads/avatars/default.png') ?>"
                     width="40" height="40"
                     class="rounded-circle">

                <div>
                    <div class="fw-bold">
                        <?= htmlspecialchars($user['username']) ?>
                        <?php if (!empty($user['verified'])): ?>
                            <i class="bi bi-patch-check-fill text-primary"></i>
                        <?php endif; ?>
                    </div>

                    <div class="text-muted small">
                        <?= htmlspecialchars($user['display_name'] ?? '') ?>
                    </div>
                </div>

            </a>

        <?php endforeach; ?>

    <?php endif; ?>

</div>

<?php include 'includes/footer.php'; ?>