<?php

use App\Core\View;

$activeSiteTab = $activeSiteTab ?? 'home';
$user = $_SESSION['user'] ?? null;
$initials = strtoupper(substr($user['full_name'] ?? 'U', 0, 1));
$authRedirectQuery = $activeSiteTab === 'home' ? '?redirect=' . rawurlencode('/') : '';

if ($user === null) {
    $tabs = [
        'home' => ['label' => 'Home', 'url' => '/'],
        'templates' => ['label' => 'Templates', 'url' => '/cv/templates'],
    ];
} elseif (($user['role'] ?? null) === 'employer') {
    $tabs = [
        'home' => ['label' => 'Home', 'url' => '/'],
        'find-cvs' => ['label' => 'Find CVs', 'url' => '/find-cvs'],
        'my-jobs' => ['label' => 'My Jobs', 'url' => '/employer/jobs'],
        'post-job' => ['label' => 'Post Job', 'url' => '/employer/jobs/create'],
        'profile' => ['label' => 'Profile', 'url' => '/profiles'],
    ];
} else {
    $tabs = [
        'home' => ['label' => 'Home', 'url' => '/'],
        'job-search' => ['label' => 'Jobs', 'url' => '/jobs'],
        'templates' => ['label' => 'Templates', 'url' => '/cv/templates'],
        'builder' => ['label' => 'CV Builder', 'url' => '/cv/edit'],
        'profile' => ['label' => 'Profile', 'url' => '/profiles'],
    ];
}
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
                <a class="job-avatar" href="<?= View::url('/profile') ?>" title="<?= View::e($user['full_name'] ?? 'User') ?>" aria-label="Open profile">
                    <?php if (! empty($user['avatar_url'])): ?>
                        <img src="<?= View::e($user['avatar_url']) ?>" alt="<?= View::e($user['full_name'] ?? 'User') ?> avatar">
                    <?php else: ?>
                        <?= View::e($initials) ?>
                    <?php endif; ?>
                </a>

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
