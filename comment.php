<?php

require_once 'includes/db.php';
require_once 'includes/auth.php';

if (!loggedIn()) exit;

$videoId = (int)($_POST['video_id'] ?? 0);
$text = trim($_POST['comment'] ?? '');
$parentId = isset($_POST['parent_id']) && $_POST['parent_id'] !== ''
    ? (int)$_POST['parent_id']
    : null;

$image = null;

if (!empty($_FILES['image']['name'])) {
    $image = time() . '_' . basename($_FILES['image']['name']);
    move_uploaded_file($_FILES['image']['tmp_name'], "uploads/comments/" . $image);
}

$stmt = $pdo->prepare("
INSERT INTO comments (video_id, user_id, comment, image, parent_id)
VALUES (?, ?, ?, ?, ?)
");

$stmt->execute([
    $videoId,
    currentUserId(),
    $text,
    $image,
    $parentId
]);

header("Location: " . ($_SERVER['HTTP_REFERER'] ?? '/'));
exit;