<?php

use App\Core\View;

$old = $_SESSION['_old'] ?? [];
?>
<main class="auth-page">
    <section class="auth-shell">
        <aside class="auth-brand">
            <div class="brand-overlay"></div>
            <div class="brand-content">
                <p class="eyebrow">Account Recovery</p>
                <h1>OneCV</h1>
                <p class="brand-copy">
                    Enter your email and we will send a secure reset link to your mailbox.
                </p>

                <div class="brand-points">
                    <div class="brand-point">
                        <span class="brand-icon">↺</span>
                        <div>
                            <strong>Reset link</strong>
                            <span>The link expires after 60 minutes.</span>
                        </div>
                    </div>
                    <div class="brand-point">
                        <span class="brand-icon">✓</span>
                        <div>
                            <strong>Secure token</strong>
                            <span>Only a hashed token is stored in the database.</span>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <section class="auth-panel">
            <div class="auth-heading">
                <h2>Forgot Password</h2>
                <p>We will email you a link to create a new password.</p>
            </div>

            <form class="auth-form" method="post" action="<?= View::url('/forgot-password') ?>">
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

                <button class="primary-button" type="submit">Send Reset Link</button>
            </form>

            <p class="auth-helper-link">
                Remember your password?
                <a href="<?= View::url('/login') ?>">Back to login</a>
            </p>
        </section>
    </section>
</main>
