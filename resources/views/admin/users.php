<?php

use App\Core\View;

$roleLabels = [
    'job_seeker' => 'Job Seekers',
    'employer' => 'Employers',
    'admin' => 'Administrators',
];
$statusClass = static fn (string $status): string => $status === 'active' ? 'active' : 'inactive';
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
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($users === []): ?>
                            <tr>
                                <td colspan="6" class="empty-cell">No users in this role yet.</td>
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
                                    <span class="status-pill <?= $statusClass((string) $user['status']) ?>">
                                        <?= View::e(ucfirst((string) $user['status'])) ?>
                                    </span>
                                </td>
                                <td class="text-right">
                                    <button
                                        class="table-icon-button js-open-user-status-modal"
                                        type="button"
                                        data-id="<?= (int) $user['id'] ?>"
                                        data-name="<?= View::e($user['full_name']) ?>"
                                        data-email="<?= View::e($user['email']) ?>"
                                        data-status="<?= View::e((string) $user['status']) ?>"
                                    >
                                        Update Status
                                    </button>
                                    <form method="post" action="<?= View::url('/admin/user-management/user/delete') ?>" class="inline-form" onsubmit="return confirm('Remove this user? Related CVs, companies, and job postings may also be removed by database cascade rules.');">
                                        <input type="hidden" name="id" value="<?= (int) $user['id'] ?>">
                                        <input type="hidden" name="role" value="<?= View::e($selectedRole) ?>">
                                        <button class="table-icon-button danger" type="submit">Remove</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <div class="reference-modal" id="user-status-modal" hidden>
            <div class="reference-modal-backdrop js-close-user-status-modal"></div>
            <section class="reference-modal-panel" role="dialog" aria-modal="true" aria-labelledby="user-status-modal-title">
                <div class="reference-modal-heading">
                    <div>
                        <p class="eyebrow">User Access</p>
                        <h2 id="user-status-modal-title">Update User Status</h2>
                        <p id="user-status-modal-summary"></p>
                    </div>
                    <button class="modal-close-button js-close-user-status-modal" type="button">Close</button>
                </div>

                <form class="reference-form reference-modal-form" method="post" action="<?= View::url('/admin/user-management/user/status') ?>">
                    <input type="hidden" name="id" id="user-status-modal-id">
                    <input type="hidden" name="role" value="<?= View::e($selectedRole) ?>">

                    <label>
                        <span>Status</span>
                        <select name="status" id="user-status-modal-status" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </label>

                    <div class="reference-modal-actions">
                        <button class="secondary-button js-close-user-status-modal" type="button">Cancel</button>
                        <button class="primary-button" type="submit">Update Status</button>
                    </div>
                </form>
            </section>
        </div>
    </section>
</main>
