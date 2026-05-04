<?php

use App\Core\View;

$activeSiteTab = $activeSiteTab ?? 'home';
$user = $_SESSION['user'] ?? null;
$initials = strtoupper(substr($user['full_name'] ?? 'U', 0, 1));
$authRedirectQuery = $activeSiteTab === 'home' ? '?redirect=' . rawurlencode('/') : '';

$tabs = $user === null
    ? [
        'home' => ['label' => 'Home', 'url' => '/'],
        'templates' => ['label' => 'Templates', 'url' => '/cv/templates'],
    ]
    : [
        'home' => ['label' => 'Home', 'url' => '/'],
        'templates' => ['label' => 'Templates', 'url' => '/cv/templates'],
        'builder' => ['label' => 'CV Builder', 'url' => '/cv/edit'],
        'find-cvs' => ['label' => 'Find CVs', 'url' => '/find-cvs'],
        'profile' => ['label' => 'Profiles', 'url' => '/profiles'],
    ];
?>
<header class="job-topbar">
    <div class="job-topbar-inner">
        <div class="job-topbar-left">
            <a class="job-brand" href="<?= View::url('/') ?>">
                <strong>OneCV</strong>
                <span>Premium CV Builder</span>
            </a>

            <nav class="job-nav" aria-label="Main navigation">
                <?php foreach ($tabs as $key => $tab): ?>
                    <a
                        class="<?= $activeSiteTab === $key ? 'active' : '' ?>"
                        href="<?= View::url($tab['url']) ?>"
                    >
                        <?= View::e($tab['label']) ?>
                    </a>
                <?php endforeach; ?>
            </nav>
        </div>

        <div class="job-topbar-actions">
            <?php if ($user === null): ?>
                <a class="site-login-link" href="<?= View::url('/login' . $authRedirectQuery) ?>">Login</a>
                <a class="site-register-link" href="<?= View::url('/register' . $authRedirectQuery) ?>">Register</a>
            <?php else: ?>
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
            <?php endif; ?>
        </div>
    </div>
</header>
