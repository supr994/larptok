<?php

require_once 'includes/db.php';
require_once 'includes/auth.php';

if (!loggedIn()) {
    header("Location: /login.php");
    exit;
}


if (!isset($_SESSION['watched_videos'])) {
    $_SESSION['watched_videos'] = [];
}

$stmt = $pdo->query("
SELECT
    videos.*,
    users.username,
    users.display_name,
    users.avatar,
    users.verified,
    videos.views,
    (SELECT COUNT(*) FROM likes WHERE video_id = videos.id) AS likes_count,
    (SELECT COUNT(*) FROM comments WHERE video_id = videos.id) AS comments_count
FROM videos
INNER JOIN users ON users.id = videos.user_id
ORDER BY videos.id DESC
");

$videos = $stmt->fetchAll();

include 'includes/header.php';

foreach ($videos as $video):
?>

<?php
if (!in_array($video['id'], $_SESSION['watched_videos'])) {
    $pdo->prepare("UPDATE videos SET views = views + 1 WHERE id = ?")
        ->execute([$video['id']]);

    $_SESSION['watched_videos'][] = $video['id'];
}
?>

<div class="position-relative mb-4 bg-black overflow-hidden rounded">

    <video class="tiktok-video w-100"
           muted
           playsinline
           controls
           style="height:80vh; object-fit:cover;">
        <source src="/uploads/videos/<?= htmlspecialchars($video['filename']) ?>">
    </video>


    <div class="position-absolute top-50 end-0 translate-middle-y me-2 d-flex flex-column gap-2">

        <a href="/like.php?id=<?= $video['id'] ?>" class="btn text-white text-center">
            <i class="bi bi-heart"></i><br>
            <?= $video['likes_count'] ?>
        </a>

        <button class="btn text-white text-center"
                onclick="openComments(<?= $video['id'] ?>)">
            <i class="bi bi-chat"></i><br>
            <?= $video['comments_count'] ?>
        </button>

        <div class="btn text-white text-center">
            <i class="bi bi-play"></i><br>
            <?= $video['views'] ?>
        </div>

    </div>


    <div class="position-absolute bottom-0 start-0 text-white p-3 w-75">

        <div class="d-flex align-items-center gap-2">

            <?php if (!empty($video['avatar'])): ?>
                <img src="/<?= htmlspecialchars($video['avatar']) ?>"
                     width="34" height="34"
                     class="rounded-circle">
            <?php endif; ?>

            <strong>
                <a href="/profile.php?user=<?= urlencode($video['username']) ?>"
                   class="text-white text-decoration-none">
                    @<?= htmlspecialchars($video['username']) ?>
                </a>

                <?php if (!empty($video['verified'])): ?>
                    <i class="bi bi-patch-check-fill text-primary"></i>
                <?php endif; ?>
            </strong>

        </div>

        <div class="small mt-2">
            <?= htmlspecialchars($video['caption']) ?>
        </div>

    </div>
</div>


<div id="comments-<?= $video['id'] ?>"
     class="position-fixed top-0 end-0 h-100 bg-dark text-white p-3"
     style="width:350px; display:none; overflow-y:auto; z-index:9999;">

    <div class="d-flex justify-content-between mb-2">
        <h5>Comments</h5>
        <button class="btn btn-sm btn-light"
                onclick="closeComments(<?= $video['id'] ?>)">X</button>
    </div>

    <form action="/comment.php" method="post" enctype="multipart/form-data">

        <input type="hidden" name="video_id" value="<?= $video['id'] ?>">
        <input type="hidden" name="parent_id" id="parent_id-<?= $video['id'] ?>">

        <div class="input-group">

            <input type="text" name="comment" class="form-control" placeholder="Write a comment">

            <label class="btn btn-secondary">
                <i class="bi bi-image"></i>
                <input type="file" name="image" hidden>
            </label>

            <button class="btn btn-primary">
                <i class="bi bi-send"></i>
            </button>

        </div>

    </form>

    <?php
    $comments = $pdo->prepare("
        SELECT c.*, u.username, u.avatar, u.verified
        FROM comments c
        INNER JOIN users u ON u.id = c.user_id
        WHERE video_id = ?
        ORDER BY c.id DESC
    ");
    $comments->execute([$video['id']]);
    $comments = $comments->fetchAll();

    foreach ($comments as $comment):
    ?>

    <div class="d-flex gap-2 mt-3">

        <?php if (!empty($comment['avatar'])): ?>
            <img src="/<?= htmlspecialchars($comment['avatar']) ?>"
                 width="28" height="28"
                 class="rounded-circle">
        <?php endif; ?>

        <div>

            <div class="fw-bold">
                <a href="/profile.php?user=<?= urlencode($comment['username']) ?>"
                   class="text-white text-decoration-none">
                    @<?= htmlspecialchars($comment['username']) ?>
                </a>

                <?php if (!empty($comment['verified'])): ?>
                    <i class="bi bi-patch-check-fill text-primary"></i>
                <?php endif; ?>
            </div>

            <div><?= htmlspecialchars($comment['comment']) ?></div>

            <?php if (!empty($comment['image'])): ?>
                <img src="/uploads/comments/<?= htmlspecialchars($comment['image']) ?>"
                     style="max-width:180px;border-radius:8px;">
            <?php endif; ?>
<hr>
            <button class="btn btn-sm btn-outline-light mt-1"
                    onclick="setReply(<?= $video['id'] ?>, <?= $comment['id'] ?>)">
                Reply
            </button>

        </div>

    </div>

    <?php endforeach; ?>

</div>

<?php endforeach; ?>

<script>
function openComments(id){
    document.getElementById("comments-" + id).style.display = "block";
}

function closeComments(id){
    document.getElementById("comments-" + id).style.display = "none";
}

function setReply(videoId, commentId){
    const input = document.getElementById("parent_id-" + videoId);
    if (!input) return;

    input.value = commentId;
}
</script>

<?php include 'includes/footer.php'; ?>