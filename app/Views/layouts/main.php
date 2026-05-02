<?php

use App\Core\View;

$app = require dirname(__DIR__, 3) . '/config/app.php';
$title = $title ?? $app['name'];
$errors = $_SESSION['_flash']['errors'] ?? [];
$success = $_SESSION['_flash']['success'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= View::e($title) ?> | <?= View::e($app['name']) ?></title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap">
    <link rel="stylesheet" href="<?= View::asset('css/app.css') ?>">
    <script defer src="<?= View::asset('js/app.js') ?>"></script>
</head>
<body>
    <?php if ($errors !== [] || $success !== null): ?>
        <div class="flash-stack" aria-live="polite">
            <?php if ($success !== null): ?>
                <div class="flash flash-success"><?= View::e($success) ?></div>
            <?php endif; ?>

            <?php foreach ($errors as $error): ?>
                <div class="flash flash-error"><?= View::e($error) ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?= $content ?>
</body>
</html>
