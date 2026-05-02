<?php

use App\Core\View;

$activeJobSeekerTab = $activeJobSeekerTab ?? 'builder';
$user = $_SESSION['user'] ?? [];
$initials = strtoupper(substr($user['full_name'] ?? 'U', 0, 1));

$tabs = [
    'templates' => ['label' => 'Templates', 'url' => '/cv/templates'],
    'builder' => ['label' => 'CV Builder', 'url' => '/cv/edit'],
    'profile' => ['label' => 'Profile', 'url' => '/profile'],
];
?>
<header class="job-topbar">
    <div class="job-topbar-inner">
        <div class="job-topbar-left">
            <a class="job-brand" href="<?= View::url('/dashboard') ?>">
                <strong>OneCV</strong>
                <span>Premium CV Builder</span>
            </a>

            <nav class="job-nav" aria-label="Job seeker navigation">
                <?php foreach ($tabs as $key => $tab): ?>
                    <a
                        class="<?= $activeJobSeekerTab === $key ? 'active' : '' ?>"
                        href="<?= View::url($tab['url']) ?>"
                    >
                        <?= View::e($tab['label']) ?>
                    </a>
                <?php endforeach; ?>
            </nav>
        </div>

        <div class="job-topbar-actions">
            <button class="job-icon-button" type="button" aria-label="Notifications">
                <span>notifications</span>
            </button>

            <div class="job-avatar" title="<?= View::e($user['full_name'] ?? 'User') ?>">
                <?= View::e($initials) ?>
            </div>

            <form method="post" action="<?= View::url('/logout') ?>">
                <button class="job-logout-button" type="submit">
                    <span>logout</span>
                    Logout
                </button>
            </form>
        </div>
    </div>
</header>
