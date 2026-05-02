<?php

use App\Core\View;
?>
<main class="dashboard-page">
    <section class="dashboard-card">
        <p class="eyebrow">Signed In</p>
        <h1>Hello, <?= View::e($user['full_name']) ?></h1>
        <p>
            You are logged in as <strong><?= View::e($user['role']) ?></strong>.
            The next step is to connect this dashboard to each role's workflow.
        </p>

        <div class="dashboard-actions">
            <?php if ($user['role'] === 'job_seeker'): ?>
                <a class="secondary-button" href="<?= View::url('/cv/edit') ?>">Manage CV</a>
            <?php elseif ($user['role'] === 'employer'): ?>
                <a class="secondary-button" href="<?= View::url('/search') ?>">Search CVs</a>
            <?php elseif ($user['role'] === 'admin'): ?>
                <a class="secondary-button" href="<?= View::url('/admin/users') ?>">Admin Users</a>
            <?php endif; ?>

            <form method="post" action="<?= View::url('/logout') ?>">
                <button class="primary-button" type="submit">Logout</button>
            </form>
        </div>
    </section>
</main>
