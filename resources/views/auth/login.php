<?php

use App\Core\View;

$old = $_SESSION['_old'] ?? [];
$selectedRole = $old['role'] ?? 'job_seeker';
$redirectPath = $redirect ?? $old['redirect'] ?? '';
$redirectQuery = $redirectPath !== '' ? '?redirect=' . rawurlencode($redirectPath) : '';
?>
<main class="auth-page">
    <section class="auth-shell">
        <aside class="auth-brand">
            <div class="brand-overlay"></div>
            <div class="brand-content">
                <p class="eyebrow">Curating Careers</p>
                <h1>OneCV</h1>
                <p class="brand-copy">
                    Structured CV tools for job seekers, employers, and administrators.
                </p>

                <div class="brand-points">
                    <div class="brand-point">
                        <span class="brand-icon">✓</span>
                        <div>
                            <strong>Normalized CV data</strong>
                            <span>Search-ready profiles with structured fields.</span>
                        </div>
                    </div>
                    <div class="brand-point">
                        <span class="brand-icon">↗</span>
                        <div>
                            <strong>Role-based portal</strong>
                            <span>Separate flows for job seekers, employers, and admins.</span>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <section class="auth-panel">
            <div class="auth-heading">
                <h2>Login</h2>
                <p>Enter your account credentials to continue.</p>
            </div>

            <div class="auth-tabs">
                <a class="active" href="<?= View::url('/login' . $redirectQuery) ?>">Login</a>
                <a href="<?= View::url('/register' . $redirectQuery) ?>">Register</a>
            </div>

            <form class="auth-form" method="post" action="<?= View::url('/login') ?>">
                <?php if ($redirectPath !== ''): ?>
                    <input type="hidden" name="redirect" value="<?= View::e($redirectPath) ?>">
                <?php endif; ?>

                <fieldset class="role-grid role-grid-three">
                    <legend>Access Role</legend>

                    <label class="role-option">
                        <input
                            type="radio"
                            name="role"
                            value="job_seeker"
                            <?= $selectedRole === 'job_seeker' ? 'checked' : '' ?>
                        >
                        <span>
                            <strong>Job Seeker</strong>
                            <small>Manage CV</small>
                        </span>
                    </label>

                    <label class="role-option">
                        <input
                            type="radio"
                            name="role"
                            value="employer"
                            <?= $selectedRole === 'employer' ? 'checked' : '' ?>
                        >
                        <span>
                            <strong>Employer</strong>
                            <small>Search CVs</small>
                        </span>
                    </label>

                    <label class="role-option">
                        <input
                            type="radio"
                            name="role"
                            value="admin"
                            <?= $selectedRole === 'admin' ? 'checked' : '' ?>
                        >
                        <span>
                            <strong>Admin</strong>
                            <small>Manage data</small>
                        </span>
                    </label>
                </fieldset>

                <label>
                    <span>Email</span>
                    <input
                        type="email"
                        name="email"
                        value="<?= View::e($old['email'] ?? '') ?>"
                        placeholder="you@example.com"
                        required
                    >
                </label>

                <label>
                    <span>Password</span>
                    <input
                        type="password"
                        name="password"
                        placeholder="Enter your password"
                        required
                    >
                </label>

                <div class="auth-inline-actions">
                    <a href="<?= View::url('/forgot-password') ?>">Forgot password?</a>
                </div>

                <button class="primary-button" type="submit">Login</button>
            </form>
        </section>
    </section>
</main>
