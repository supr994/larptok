<?php

require_once 'includes/db.php';
require_once 'includes/auth.php';

if (!loggedIn()) {
    header("Location: /login.php");
    exit;
}

$userId = currentUserId();

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();


$themes = [
    'solar',
    'vapor', 'quartz'
];

$currentTheme = $_COOKIE['theme'] ?? 'solar';

if (isset($_POST['theme'])) {

    $t = $_POST['theme'];

    if (in_array($t, $themes)) {
        setcookie('theme', $t, time() + (86400 * 365), "/");
    }

    header("Location: /settings.php");
    exit;
}



if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['theme'])) {

    $display_name = trim($_POST['display_name'] ?? '');
    $bio = trim($_POST['bio'] ?? '');

    $avatarPath = $user['avatar'] ?? null;

    if (!empty($_FILES['avatar']['name'])) {

        $ext = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','webp'];

        if (in_array($ext, $allowed)) {

            $dir = "uploads/avatars/";

            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }

            $avatarPath = $dir . time() . "_" . rand(1000,9999) . "." . $ext;

            move_uploaded_file($_FILES['avatar']['tmp_name'], $avatarPath);
        }
    }

    $stmt = $pdo->prepare("
        UPDATE users
        SET display_name = ?, bio = ?, avatar = ?
        WHERE id = ?
    ");

    $stmt->execute([
        $display_name,
        $bio,
        $avatarPath,
        $userId
    ]);

    header("Location: /settings.php");
    exit;
}

include 'includes/header.php';
?>

<div class="container text-white mt-3">

<h2>Settings</h2>
<hr>

<form method="post" enctype="multipart/form-data">


    <div class="mb-3">
        <label>Profile Picture</label><br>

        <img src="/<?= htmlspecialchars($user['avatar'] ?? 'uploads/avatars/default.png') ?>"
             width="60"
             height="60"
             class="rounded-circle mb-2">

        <br>

        <label class="btn btn-outline-secondary">
            <i class="bi bi-image"></i>
            <input type="file" name="avatar" hidden>
        </label>
    </div>

    <div class="mb-3">
        <label>Display Name</label>
        <input type="text"
               name="display_name"
               class="form-control"
               value="<?= htmlspecialchars($user['display_name'] ?? '') ?>">
    </div>

    <div class="mb-3">
        <label>Bio</label>
        <textarea name="bio"
                  class="form-control"><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
    </div>

    <button class="btn btn-primary mb-3">
        Save
    </button>

</form>



<h2>Theme</h2>

<hr>
<form method="post">

    <div class="row g-2 mb-3">

        <?php foreach ($themes as $theme): ?>

            <div class="col-6 col-md-3">

                <button name="theme"
                        value="<?= $theme ?>"
                        class="btn btn-sm w-100 <?= $currentTheme === $theme ? 'btn-primary' : 'btn-outline-secondary' ?>">

                    <?= ucfirst($theme) ?>

                </button>

            </div>

        <?php endforeach; ?>

    </div>

</form>

</div>

<?php include 'includes/footer.php'; ?>