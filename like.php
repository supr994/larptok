<?php

require_once 'includes/db.php';
require_once 'includes/auth.php';

if (!loggedIn()) {
    exit;
}

$videoId = (int)($_GET['id'] ?? 0);

$check = $pdo->prepare("
SELECT id
FROM likes
WHERE user_id = ?
AND video_id = ?
");

$check->execute([
    currentUserId(),
    $videoId
]);

if ($check->fetch()) {

    $delete = $pdo->prepare("
    DELETE FROM likes
    WHERE user_id = ?
    AND video_id = ?
    ");

    $delete->execute([
        currentUserId(),
        $videoId
    ]);

} else {

    $insert = $pdo->prepare("
    INSERT INTO likes
    (
        user_id,
        video_id
    )
    VALUES
    (
        ?, ?
    )
    ");

    $insert->execute([
        currentUserId(),
        $videoId
    ]);
}

header("Location: " . ($_SERVER['HTTP_REFERER'] ?? '/'));