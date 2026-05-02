<?php

use App\Core\View;

$stats = $stats ?? [];
?>
<main class="admin-shell">
    <?php require __DIR__ . '/partials/sidebar.php'; ?>

    <section class="admin-main">
        <?php require __DIR__ . '/partials/topbar.php'; ?>

        <section class="admin-page-heading">
            <p class="eyebrow">Admin Console</p>
            <h1>System Overview</h1>
            <p>Real-time platform totals from the local CV management database.</p>
        </section>

        <section class="metric-grid">
            <article class="metric-card">
                <span>Total Users</span>
                <strong><?= View::e($stats['total_users'] ?? 0) ?></strong>
                <small><?= View::e($stats['job_seekers'] ?? 0) ?> job seekers · <?= View::e($stats['employers'] ?? 0) ?> employers · <?= View::e($stats['admins'] ?? 0) ?> admins</small>
            </article>

            <article class="metric-card">
                <span>Total CVs</span>
                <strong><?= View::e($stats['total_cvs'] ?? 0) ?></strong>
                <small>Structured online CV records</small>
            </article>

            <article class="metric-card">
                <span>Institutions</span>
                <strong><?= View::e($stats['institutions'] ?? 0) ?></strong>
                <small>Education lookup entries</small>
            </article>

            <article class="metric-card">
                <span>Indexed Skills</span>
                <strong><?= View::e($stats['skills'] ?? 0) ?></strong>
                <small>Selectable skills for strongest skills</small>
            </article>

            <article class="metric-card metric-wide">
                <span>CV Categories</span>
                <strong><?= View::e($stats['categories'] ?? 0) ?></strong>
                <small>Professional domains available to job seekers</small>
            </article>
        </section>
    </section>
</main>
