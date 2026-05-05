<?php

use App\Core\View;
?>
<main class="dashboard-page">
    <section class="dashboard-card">
        <p class="eyebrow">404</p>
        <h1>Page not found</h1>
        <p>The route <strong><?= View::e($path ?? '/') ?></strong> has not been implemented yet.</p>
        <a class="primary-button inline-button" href="<?= View::url('/login') ?>">Back to Login</a>
    </section>
</main>
