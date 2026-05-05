<?php

use App\Core\View;
?>
<main class="dashboard-page">
    <section class="dashboard-card">
        <p class="eyebrow">403</p>
        <h1>Access denied</h1>
        <p>You do not have permission to access this page.</p>
        <a class="primary-button inline-button" href="<?= View::url('/dashboard') ?>">Back to Dashboard</a>
    </section>
</main>
