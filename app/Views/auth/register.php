<?php

use App\Core\View;

$old = $_SESSION['_old'] ?? [];
$selectedRole = $old['role'] ?? 'job_seeker';
?>
<main class="auth-page">
    <section class="auth-shell">
        <aside class="auth-brand">
            <div class="brand-overlay"></div>
            <div class="brand-content">
                <p class="eyebrow">Curating Careers</p>
                <h1>CV Management</h1>
                <p class="brand-copy">
                    Create an account to manage online CVs or search structured candidate profiles.
                </p>

                <div class="brand-points">
                    <div class="brand-point">
                        <span class="brand-icon">1</span>
                        <div>
                            <strong>Job Seeker</strong>
                            <span>Create and maintain one complete online CV.</span>
                        </div>
                    </div>
                    <div class="brand-point">
                        <span class="brand-icon">2</span>
                        <div>
                            <strong>Employer</strong>
                            <span>Search, filter, and view CVs in read-only mode.</span>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <section class="auth-panel">
            <div class="auth-heading">
                <h2>Register</h2>
                <p>Create a new job seeker or employer account.</p>
            </div>

            <div class="auth-tabs">
                <a href="<?= View::url('/login') ?>">Login</a>
                <a class="active" href="<?= View::url('/register') ?>">Register</a>
            </div>

            <form class="auth-form" method="post" action="<?= View::url('/register') ?>">
                <fieldset class="role-grid">
                    <legend>Account Type</legend>

                    <label class="role-option">
                        <input
                            type="radio"
                            name="role"
                            value="job_seeker"
                            <?= $selectedRole === 'job_seeker' ? 'checked' : '' ?>
                        >
                        <span>
                            <strong>Job Seeker</strong>
                            <small>Create and manage one CV</small>
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
                            <small>Search and view CVs</small>
                        </span>
                    </label>
                </fieldset>

                <label>
                    <span>Full Name</span>
                    <input
                        type="text"
                        name="full_name"
                        value="<?= View::e($old['full_name'] ?? '') ?>"
                        placeholder="Nguyen Van A"
                        required
                    >
                </label>

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
                        placeholder="At least 8 characters"
                        minlength="8"
                        required
                    >
                </label>

                <label>
                    <span>Confirm Password</span>
                    <input
                        type="password"
                        name="password_confirmation"
                        placeholder="Repeat your password"
                        minlength="8"
                        required
                    >
                </label>

                <button class="primary-button" type="submit">Create Account</button>
            </form>
        </section>
    </section>
</main>
