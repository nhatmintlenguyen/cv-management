<?php

use App\Core\View;

$roleLabels = [
    'job_seeker' => 'Job Seekers',
    'employer' => 'Employers',
    'admin' => 'Administrators',
];
?>
<main class="admin-shell">
    <?php require __DIR__ . '/partials/sidebar.php'; ?>

    <section class="admin-main">
        <?php require __DIR__ . '/partials/topbar.php'; ?>

        <section class="admin-page-heading">
            <p class="eyebrow">User Management</p>
            <h1>Manage Users</h1>
            <p>Oversee platform access by role. Switching role tabs updates the URL query string.</p>
        </section>

        <section class="admin-toolbar">
            <nav class="admin-segmented">
                <?php foreach ($roles as $role): ?>
                    <a
                        class="<?= $selectedRole === $role ? 'active' : '' ?>"
                        href="<?= View::url('/admin/user-management/user?role=' . $role) ?>"
                    >
                        <?= View::e($roleLabels[$role]) ?>
                    </a>
                <?php endforeach; ?>
            </nav>

            <button class="admin-action-button" type="button">Invite User</button>
        </section>

        <section class="admin-table-card">
            <div class="table-heading">
                <div>
                    <h3><?= View::e($roleLabels[$selectedRole]) ?></h3>
                    <p><?= View::e($total) ?> users found</p>
                </div>
            </div>

            <div class="table-scroll">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Registration Date</th>
                            <th>Status</th>
                            <th>Role</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($users === []): ?>
                            <tr>
                                <td colspan="5" class="empty-cell">No users in this role yet.</td>
                            </tr>
                        <?php endif; ?>

                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td>
                                    <div class="user-cell">
                                        <span class="user-initial"><?= View::e(strtoupper(substr($user['full_name'], 0, 1))) ?></span>
                                        <strong><?= View::e($user['full_name']) ?></strong>
                                    </div>
                                </td>
                                <td><?= View::e($user['email']) ?></td>
                                <td><?= View::e(date('M d, Y', strtotime($user['created_at']))) ?></td>
                                <td>
                                    <span class="status-pill <?= $user['status'] === 'active' ? 'active' : 'inactive' ?>">
                                        <?= View::e($user['status']) ?>
                                    </span>
                                </td>
                                <td><?= View::e($user['role_name']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </section>
</main>
