<?php

use App\Core\View;

$activeJobSeekerTab = 'profile';
?>
<?php require dirname(__DIR__) . '/job-seeker/partials/topbar.php'; ?>

<main class="job-page">
    <section class="job-page-heading">
        <p class="eyebrow">Profile</p>
        <h1><?= View::e($user['full_name'] ?? 'Your Profile') ?></h1>
        <p><?= View::e($user['email'] ?? '') ?></p>
    </section>
</main>
