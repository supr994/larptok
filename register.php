<?php

require_once 'includes/db.php';
require_once 'includes/auth.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (loggedIn()) {
    header("Location: /index.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $displayName = trim($_POST['display_name'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

   if (
    empty($username) ||
    empty($displayName) ||
    empty($password) ||
    empty($confirmPassword)
) {
    $error = 'Please fill in all fields.';

} elseif (strlen($username) < 3 || strlen($username) > 20) {
    $error = 'Username must be between 3 and 20 characters.';

} elseif (strlen($displayName) > 30) {
    $error = 'Display name cannot be more than 30 characters.';

} elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
    $error = 'Invalid username.';

} elseif ($password !== $confirmPassword) {
    $error = 'Passwords do not match.';

} elseif (strlen($password) < 6) {
    $error = 'Password must be at least 6 characters.';
} else {

        $check = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $check->execute([$username]);

        if ($check->fetch()) {

            $error = 'Username already exists.';

        } else {

            $hash = password_hash($password, PASSWORD_DEFAULT);

            $insert = $pdo->prepare("
                INSERT INTO users (username, display_name, password_hash)
                VALUES (?, ?, ?)
            ");

            $insert->execute([
                $username,
                $displayName,
                $hash
            ]);

            header("Location: /login.php");
            exit;
        }
    }
}

include 'includes/header.php';
?>

<div class="row justify-content-center">

    <div class="col-md-6">

        <div class="card">

            <div class="card-header">
                <h3 class="mb-0">Create Account</h3>
            </div>

            <div class="card-body">

                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                    </div>
                <?php endif; ?>

                <form method="post">

                    <div class="mb-3">
                        <label>Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Display Name</label>
                        <input type="text" name="display_name" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Confirm Password</label>
                        <input type="password" name="confirm_password" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        Register
                    </button>

                </form>

                <hr>

                <p class="text-center mb-0">
                    Already have an account?
                    <a href="/login.php">Login</a>
                </p>

            </div>

        </div>

    </div>

</div>

<?php include 'includes/footer.php'; ?>