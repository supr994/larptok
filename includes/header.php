
<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';
?>

<?php $theme = $_COOKIE['theme'] ?? 'solar'; ?>

<link rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.3/dist/<?= $theme ?>/bootstrap.min.css">

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>LarpTok</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet">


<link href="/assets/css/style.css" rel="stylesheet">

<style>

.sidebar {
    position: fixed;
    left: 0;
    top: 0;
    width: 240px;
    height: 100vh;

    padding: 15px;
    display: flex;
    flex-direction: column;
    gap: 10px;
}


.main-content {
    margin-left: 240px;
    padding: 20px;
}

.sidebar a {
    color: white;
    text-decoration: none;
    padding: 10px;
    border-radius: 8px;
    display: flex;
    gap: 10px;
    align-items: center;
}

.sidebar a:hover {
  
}

</style>

</head>

<body>

<div class="sidebar">

  <a href="index.php">
    <img src="/assets/img/logo.png" alt="Logo" style="height:60px;">
</a>

    <a href="/foryou.php"><i class="bi bi-house"></i> For You</a>
    <a href="/upload.php"><i class="bi bi-plus-circle"></i> Upload</a>
    <a href="/search.php"><i class="bi bi-search"></i> Search</a>
    <a href="/settings.php"><i class="bi bi-gear"></i> Settings</a>

    <?php if (loggedIn()): ?>

        <?php
        $stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $username = $stmt->fetchColumn();
        ?>

        <a href="/profile.php?user=<?= urlencode($username) ?>">
            <i class="bi bi-person"></i> Profile
        </a>

        <a href="/logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>

    <?php else: ?>


    <?php endif; ?>

</div>

<div class="main-content">