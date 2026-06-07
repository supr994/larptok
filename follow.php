<?php

require_once 'includes/db.php';
require_once 'includes/auth.php';

if (!loggedIn()) exit;

$userId = currentUserId();
$targetId = (int)$_POST['user_id'];

if ($userId == $targetId) exit;


$stmt = $pdo->prepare("
SELECT id FROM followers
WHERE follower_id = ? AND following_id = ?
");
$stmt->execute([$userId, $targetId]);

if ($stmt->fetch()) {


    $del = $pdo->prepare("
        DELETE FROM followers
        WHERE follower_id = ? AND following_id = ?
    ");
    $del->execute([$userId, $targetId]);

} else {


    $ins = $pdo->prepare("
        INSERT INTO followers (follower_id, following_id)
        VALUES (?, ?)
    ");
    $ins->execute([$userId, $targetId]);
}

header("Location: " . ($_SERVER['HTTP_REFERER'] ?? '/'));
exit;