<?php

require_once 'includes/db.php';
require_once 'includes/auth.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare(
        "SELECT * FROM users WHERE username = ?"
    );

    $stmt->execute([$username]);

    $user = $stmt->fetch();

    if (
        $user &&
        password_verify(
            $password,
            $user['password_hash']
        )
    ) {

        $_SESSION['user_id'] = $user['id'];

        header("Location: index.php");
        exit;

    } else {

        $error = "Invalid credentials.";
    }
}

include 'includes/header.php';
?>

<div class="row justify-content-center">

    <div class="col-md-6">

        <div class="card">

            <div class="card-header">
                <h3 class="mb-0">Login</h3>
            </div>

            <div class="card-body">

               <form method="post">

<input class="form-control mb-3"
       name="username"
       placeholder="Username">

<input class="form-control mb-3"
       type="password"
       name="password"
       placeholder="Password">

<button class="btn btn-primary">
    Login
</button>

</form>

                <hr>

                <p class="mb-0 text-center">
                   Don't have an account?
                    <a href="/register.php">
                        Create one for free!
                    </a>
                </p>

            </div>

        </div>

    </div>

</div>


<?php include 'includes/footer.php'; ?>