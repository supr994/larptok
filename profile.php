<?php

require_once 'includes/db.php';
require_once 'includes/auth.php';

$username = $_GET['user'] ?? '';

$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch();

if (!$user) {
    die("User not found");
}

try {
    $f1 = $pdo->prepare("SELECT COUNT(*) FROM followers WHERE following_id = ?");
    $f1->execute([$user['id']]);
    $followers = $f1->fetchColumn();

    $f2 = $pdo->prepare("SELECT COUNT(*) FROM followers WHERE follower_id = ?");
    $f2->execute([$user['id']]);
    $following = $f2->fetchColumn();
} catch (Exception $e) {
    $followers = 0;
    $following = 0;
}

$isFollowing = false;

if (loggedIn()) {
    try {
        $check = $pdo->prepare("
            SELECT id FROM followers
            WHERE follower_id = ? AND following_id = ?
        ");
        $check->execute([currentUserId(), $user['id']]);
        $isFollowing = (bool)$check->fetch();
    } catch (Exception $e) {
        $isFollowing = false;
    }
}

include 'includes/header.php';
?>


<div class="container text-white text-center mt-1 p-1">

    <img src="/<?= htmlspecialchars($user['avatar'] ?? 'default.png') ?>"
         width="75" height="75"
         class="rounded-circle border border-2">

    <h3 class="mt-1 mb-1">
        <?= htmlspecialchars($user['display_name']) ?>

        <?php if (!empty($user['verified'])): ?>
            <i class="bi bi-patch-check-fill text-primary"></i>
        <?php endif; ?>
    </h3>

    <p class="mb-1">
        @<?= htmlspecialchars($user['username']) ?>
    </p>

    <p class="mb-1 text-body-tertiary">
        “<?= nl2br(htmlspecialchars($user['bio'] ?? '')) ?>”
    </p>

    <div class="d-flex justify-content-center gap-2 mb-1">

        <div>
            <strong class="text-muted mb-3"><?= $followers ?></strong><br>
            Followers
        </div>

        <div>
            <strong class="text-muted mb-3"><?= $following ?></strong><br>
            Following
        </div>

    </div>
<p>
    <?php if (loggedIn() && currentUserId() != $user['id']): ?>

        <form action="/follow.php" method="post" class="mb-1">

            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">

            <button class="btn btn-sm <?= $isFollowing ? 'btn-outline-secondary' : 'btn-primary' ?>">
                <?= $isFollowing ? 'Unfollow' : 'Follow' ?>
            </button>

        </form>

    <?php endif; ?>

</div>
<hr>


<div class="container mt-1 p-1">

    <div class="row g-1">

        <?php
        $videos = $pdo->prepare("
            SELECT * FROM videos
            WHERE user_id = ?
            ORDER BY id DESC
        ");
        $videos->execute([$user['id']]);

        foreach ($videos as $video):
        ?>

        <div class="col-12 col-md-4">

            <div class="bg-black text-white rounded overflow-hidden">

                <video
                    class="w-100"
                    controls
                    style="aspect-ratio: 9/16; object-fit: cover;">

                    <source src="/uploads/videos/<?= htmlspecialchars($video['filename']) ?>">
                </video>

                <div class="p-1">
                    <?= htmlspecialchars($video['caption']) ?>
                </div>

            </div>

        </div>

        <?php endforeach; ?>

    </div>

</div>

<?php include 'includes/footer.php'; ?>