<?php

require_once 'includes/db.php';
require_once 'includes/auth.php';

if (!loggedIn()) {
    header("Location: /login.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $caption = trim($_POST['caption'] ?? '');

    if (
        !isset($_FILES['video']) ||
        $_FILES['video']['error'] !== UPLOAD_ERR_OK
    ) {

        $error = 'Please choose a video.';

    } else {

        $allowedTypes = [
            'video/mp4',
            'video/webm'
        ];

        $mimeType = mime_content_type(
            $_FILES['video']['tmp_name']
        );

        if (!in_array($mimeType, $allowedTypes)) {

            $error = 'Invalid video format.';

        } else {

            $extension = strtolower(
                pathinfo(
                    $_FILES['video']['name'],
                    PATHINFO_EXTENSION
                )
            );

            $filename =
                bin2hex(random_bytes(16))
                . '.'
                . $extension;

            $destination =
                __DIR__
                . '/uploads/videos/'
                . $filename;

            if (
                move_uploaded_file(
                    $_FILES['video']['tmp_name'],
                    $destination
                )
            ) {

                $stmt = $pdo->prepare("
                    INSERT INTO videos
                    (
                        user_id,
                        caption,
                        filename
                    )
                    VALUES
                    (
                        ?, ?, ?
                    )
                ");

                $stmt->execute([
                    currentUserId(),
                    $caption,
                    $filename
                ]);

                header("Location: /index.php");
                exit;

            } else {

                $error = 'Upload failed.';
            }
        }
    }
}

include 'includes/header.php';
?>

<div class="row justify-content-center">

<div class="col-md-8">



 <h2>Upload Video</h2>
    <hr>

<div class="card-body">

<?php if($error): ?>

<div class="alert alert-danger">
<?= htmlspecialchars($error) ?>
</div>

<?php endif; ?>

<form
method="post"
enctype="multipart/form-data">

<div class="mb-3">

<label class="form-label">
Caption
</label>

<textarea
name="caption"
class="form-control"
rows="3"></textarea>

</div>

<div class="mb-3">

<label class="form-label">
Video
</label>

<input
type="file"
name="video"
class="form-control"
accept=".mp4,.webm"
required>

</div>

<button
class="btn btn-primary">

Upload

</button>

</form>

</div>

</div>

</div>

</div>

<?php include 'includes/footer.php'; ?>