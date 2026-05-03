<?php

use App\Core\View;

$activeSiteTab = 'profile';
?>
<?php require dirname(__DIR__) . '/partials/site-topbar.php'; ?>

<main class="job-page">
    <section class="job-page-heading">
        <p class="eyebrow">Profile</p>
        <h1><?= View::e($user['full_name'] ?? 'Your Profile') ?></h1>
        <p><?= View::e($user['email'] ?? '') ?></p>
    </section>
</main>
