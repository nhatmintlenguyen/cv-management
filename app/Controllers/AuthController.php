<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\View;
use App\Models\PasswordReset;
use App\Models\Role;
use App\Models\User;
use App\Services\Mailer;
use RuntimeException;

class AuthController extends Controller
{
    public function showLogin(): void
    {
        $redirect = $this->safeRedirectPath($this->input('redirect'));

        if ($this->authenticated()) {
            $this->redirect($redirect ?? '/dashboard');
        }

        $this->view('auth/login', [
            'title' => 'Login',
            'redirect' => $redirect,
        ]);
    }

    public function showRegister(): void
    {
        $redirect = $this->safeRedirectPath($this->input('redirect'));

        if ($this->authenticated()) {
            $this->redirect($redirect ?? '/dashboard');
        }

        $this->view('auth/register', [
            'title' => 'Register',
            'redirect' => $redirect,
        ]);
    }

    public function showForgotPassword(): void
    {
        if ($this->authenticated()) {
            $this->redirect('/dashboard');
        }

        $this->view('auth/forgot-password', [
            'title' => 'Forgot Password',
        ]);
    }

    public function sendResetLink(): void
    {
        $email = strtolower(trim((string) $this->input('email', '')));

        if ($email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->flash('errors', ['Please enter a valid email address.']);
            $this->old(['email' => $email]);
            $this->redirect('/forgot-password');
        }

        $user = (new User())->findByEmail($email);

        if ($user !== null && $user['status'] === 'active') {
            $token = bin2hex(random_bytes(32));
            $tokenHash = hash('sha256', $token);

            (new PasswordReset())->createToken((int) $user['id'], $tokenHash);

            $resetUrl = $this->absoluteUrl('/reset-password?token=' . rawurlencode($token));

            try {
                (new Mailer())->send(
                    $email,
                    'Reset your OneCV password',
                    $this->resetEmailHtml((string) $user['full_name'], $resetUrl),
                    "Reset your OneCV password:\n{$resetUrl}\n\nThis link expires in 60 minutes."
                );
            } catch (RuntimeException $exception) {
                $this->flash('errors', ['We could not send the reset email. Please make sure Mailpit is running.']);
                $this->redirect('/forgot-password');
            }
        }

        $this->flash('success', 'If that email exists, a password reset link has been sent.');
        $this->redirect('/login');
    }

    public function showResetPassword(): void
    {
        if ($this->authenticated()) {
            $this->redirect('/dashboard');
        }

        $token = trim((string) $this->input('token', ''));
        $reset = $token === '' ? null : (new PasswordReset())->findValidByHash(hash('sha256', $token));

        if ($reset === null) {
            $this->flash('errors', ['This password reset link is invalid or expired.']);
            $this->redirect('/forgot-password');
        }

        $this->view('auth/reset-password', [
            'title' => 'Reset Password',
            'token' => $token,
            'email' => $reset['email'],
        ]);
    }

    public function resetPassword(): void
    {
        $token = trim((string) $this->input('token', ''));
        $password = (string) $this->input('password', '');
        $passwordConfirmation = (string) $this->input('password_confirmation', '');
        $reset = $token === '' ? null : (new PasswordReset())->findValidByHash(hash('sha256', $token));

        if ($reset === null) {
            $this->flash('errors', ['This password reset link is invalid or expired.']);
            $this->redirect('/forgot-password');
        }

        $errors = $this->validatePasswordReset($password, $passwordConfirmation);

        if ($errors !== []) {
            $this->flash('errors', $errors);
            $this->redirect('/reset-password?token=' . rawurlencode($token));
        }

        $userModel = new User();
        $userModel->updatePassword((int) $reset['user_id'], $password);
        (new PasswordReset())->markUsed((int) $reset['id']);

        $this->flash('success', 'Your password has been reset. Please log in with your new password.');
        $this->redirect('/login');
    }

    public function register(): void
    {
        $data = $this->only(['full_name', 'email', 'password', 'password_confirmation', 'role', 'redirect']);
        $redirect = $this->safeRedirectPath($data['redirect'] ?? null);
        $errors = $this->validateRegistration($data);

        if ($errors !== []) {
            $this->flash('errors', $errors);
            $this->old([
                'full_name' => $data['full_name'] ?? '',
                'email' => $data['email'] ?? '',
                'role' => $data['role'] ?? 'job_seeker',
                'redirect' => $redirect ?? '',
            ]);
            $this->redirect('/register' . $this->redirectQuery($redirect));
        }

        $role = (new Role())->findByName($data['role']);

        if ($role === null) {
            $this->flash('errors', ['Selected role is not available.']);
            $this->old([
                'full_name' => $data['full_name'] ?? '',
                'email' => $data['email'] ?? '',
                'role' => $data['role'] ?? 'job_seeker',
                'redirect' => $redirect ?? '',
            ]);
            $this->redirect('/register' . $this->redirectQuery($redirect));
        }

        $userId = (new User())->createUser([
            'role_id' => (int) $role['id'],
            'full_name' => $data['full_name'],
            'email' => strtolower($data['email']),
            'password' => $data['password'],
            'status' => 'active',
        ]);

        $this->loginUser($userId);
        $this->flash('success', 'Your account has been created.');
        if ($redirect !== null) {
            $this->redirect($redirect);
        }

        $this->redirect('/dashboard');
    }

    public function login(): void
    {
        $data = $this->only(['email', 'password', 'role', 'redirect']);
        $redirect = $this->safeRedirectPath($data['redirect'] ?? null);
        $errors = [];
        $allowedRoles = ['job_seeker', 'employer', 'admin'];

        if (empty($data['email']) || ! filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Please enter a valid email address.';
        }

        if (empty($data['password'])) {
            $errors[] = 'Please enter your password.';
        }

        if (empty($data['role']) || ! in_array($data['role'], $allowedRoles, true)) {
            $errors[] = 'Please choose a valid access role.';
        }

        if ($errors !== []) {
            $this->flash('errors', $errors);
            $this->old([
                'email' => $data['email'] ?? '',
                'role' => $data['role'] ?? 'job_seeker',
                'redirect' => $redirect ?? '',
            ]);
            $this->redirect('/login' . $this->redirectQuery($redirect));
        }

        $userModel = new User();
        $user = $userModel->findByEmail(strtolower($data['email']));
        $role = $user === null ? null : $userModel->getRole((int) $user['id']);

        if (
            $user === null
            || $user['status'] !== 'active'
            || $role === null
            || $role['name'] !== $data['role']
            || ! password_verify($data['password'], $user['password_hash'])
        ) {
            $this->flash('errors', ['Invalid email, password, or selected role.']);
            $this->old([
                'email' => $data['email'] ?? '',
                'role' => $data['role'] ?? 'job_seeker',
                'redirect' => $redirect ?? '',
            ]);
            $this->redirect('/login' . $this->redirectQuery($redirect));
        }

        $this->loginUser((int) $user['id']);
        $this->flash('success', 'Welcome back.');

        if ($redirect !== null && $redirect !== '/') {
            $this->redirect($redirect);
        }

        $this->redirect($this->defaultPathForRole((string) $role['name']));
    }

    public function logout(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
        session_start();
        $this->flash('success', 'You have been logged out.');
        $this->redirect('/');
    }

    private function validateRegistration(array $data): array
    {
        $errors = [];
        $role = $data['role'] ?? '';
        $allowedRoles = ['job_seeker', 'employer'];

        if (empty($data['full_name']) || strlen($data['full_name']) < 2) {
            $errors[] = 'Full name must be at least 2 characters.';
        }

        if (empty($data['email']) || ! filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Please enter a valid email address.';
        } elseif ((new User())->findByEmail(strtolower($data['email'])) !== null) {
            $errors[] = 'This email is already registered.';
        }

        if (empty($data['password']) || strlen($data['password']) < 8) {
            $errors[] = 'Password must be at least 8 characters.';
        }

        if (($data['password'] ?? '') !== ($data['password_confirmation'] ?? '')) {
            $errors[] = 'Password confirmation does not match.';
        }

        if (! in_array($role, $allowedRoles, true)) {
            $errors[] = 'Please choose either Job Seeker or Employer.';
        }

        return $errors;
    }

    private function validatePasswordReset(string $password, string $passwordConfirmation): array
    {
        $errors = [];

        if (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters.';
        }

        if ($password !== $passwordConfirmation) {
            $errors[] = 'Password confirmation does not match.';
        }

        return $errors;
    }

    private function loginUser(int $userId): void
    {
        session_regenerate_id(true);

        $userModel = new User();
        $user = $userModel->find($userId);
        $role = $userModel->getRole($userId);

        $_SESSION['user'] = [
            'id' => (int) $user['id'],
            'full_name' => $user['full_name'],
            'email' => $user['email'],
            'avatar_url' => $user['avatar_url'] ?? null,
            'role' => $role['name'] ?? null,
        ];
    }

    private function authenticated(): bool
    {
        return isset($_SESSION['user']['id']);
    }

    private function defaultPathForRole(string $role): string
    {
        return match ($role) {
            'admin' => '/admin/overview',
            'job_seeker' => '/cv/templates',
            'employer' => '/find-cvs',
            default => '/dashboard',
        };
    }

    private function safeRedirectPath(mixed $path): ?string
    {
        if (! is_string($path) || $path === '') {
            return null;
        }

        if (! str_starts_with($path, '/') || str_starts_with($path, '//')) {
            return null;
        }

        return $path;
    }

    private function redirectQuery(?string $redirect): string
    {
        return $redirect === null ? '' : '?redirect=' . rawurlencode($redirect);
    }

    private function absoluteUrl(string $path): string
    {
        $scheme = (! empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

        return $scheme . '://' . $host . View::url($path);
    }

    private function resetEmailHtml(string $fullName, string $resetUrl): string
    {
        $name = htmlspecialchars($fullName, ENT_QUOTES, 'UTF-8');
        $url = htmlspecialchars($resetUrl, ENT_QUOTES, 'UTF-8');

        return <<<HTML
            <h1>Reset your OneCV password</h1>
            <p>Hello {$name},</p>
            <p>Click the button below to create a new password. This link expires in 60 minutes.</p>
            <p><a href="{$url}">Reset password</a></p>
            <p>If you did not request this, you can safely ignore this email.</p>
        HTML;
    }
}
