<?php

use App\Core\View;

$activeTab = $activeTab ?? '';
?>
<aside class="admin-sidebar">
    <div class="admin-brand">
        <h1>OneCV</h1>
        <p>Admin Console</p>
    </div>

    <nav class="admin-nav">
        <a class="<?= $activeTab === 'overview' ? 'active' : '' ?>" href="<?= View::url('/admin/overview') ?>">
            <span>dashboard</span>
            <strong>Dashboard</strong>
        </a>

        <a class="<?= $activeTab === 'users' ? 'active' : '' ?>" href="<?= View::url('/admin/user-management/user?role=job_seeker') ?>">
            <span>group</span>
            <strong>User Management</strong>
        </a>

        <a class="<?= $activeTab === 'reference' ? 'active' : '' ?>" href="<?= View::url('/admin/reference-management?type=skills') ?>">
            <span>database</span>
            <strong>Reference Data</strong>
        </a>
    </nav>

    <form class="admin-logout" method="post" action="<?= View::url('/logout') ?>">
        <button type="submit">
            <span>logout</span>
            <strong>Sign Out</strong>
        </button>
    </form>
</aside>
