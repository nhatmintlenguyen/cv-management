<?php

use App\Core\View;

$token = $token ?? '';
$email = $email ?? '';
?>
<main class="auth-page">
    <section class="auth-shell">
        <aside class="auth-brand">
            <div class="brand-overlay"></div>
            <div class="brand-content">
                <p class="eyebrow">New Credentials</p>
                <h1>OneCV</h1>
                <p class="brand-copy">
                    Choose a new password for your account and continue managing your CV workspace.
                </p>

                <div class="brand-points">
                    <div class="brand-point">
                        <span class="brand-icon">1</span>
                        <div>
                            <strong>Minimum length</strong>
                            <span>Your password must have at least 8 characters.</span>
                        </div>
                    </div>
                    <div class="brand-point">
                        <span class="brand-icon">2</span>
                        <div>
                            <strong>Single-use link</strong>
                            <span>The reset token is invalidated after use.</span>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <section class="auth-panel">
            <div class="auth-heading">
                <h2>Reset Password</h2>
                <p>Create a new password for <?= View::e($email) ?>.</p>
            </div>

            <form class="auth-form" method="post" action="<?= View::url('/reset-password') ?>">
                <input type="hidden" name="token" value="<?= View::e($token) ?>">

                <label>
                    <span>New Password</span>
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
                        placeholder="Repeat your new password"
                        minlength="8"
                        required
                    >
                </label>

                <button class="primary-button" type="submit">Reset Password</button>
            </form>

            <p class="auth-helper-link">
                Already reset it?
                <a href="<?= View::url('/login') ?>">Back to login</a>
            </p>
        </section>
    </section>
</main>
