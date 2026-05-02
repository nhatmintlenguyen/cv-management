<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Role;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin(): void
    {
        if ($this->authenticated()) {
            $this->redirect('/dashboard');
        }

        $this->view('auth/login', [
            'title' => 'Login',
        ]);
    }

    public function showRegister(): void
    {
        if ($this->authenticated()) {
            $this->redirect('/dashboard');
        }

        $this->view('auth/register', [
            'title' => 'Register',
        ]);
    }

    public function register(): void
    {
        $data = $this->only(['full_name', 'email', 'password', 'password_confirmation', 'role']);
        $errors = $this->validateRegistration($data);

        if ($errors !== []) {
            $this->flash('errors', $errors);
            $this->old([
                'full_name' => $data['full_name'] ?? '',
                'email' => $data['email'] ?? '',
                'role' => $data['role'] ?? 'job_seeker',
            ]);
            $this->redirect('/register');
        }

        $role = (new Role())->findByName($data['role']);

        if ($role === null) {
            $this->flash('errors', ['Selected role is not available.']);
            $this->old([
                'full_name' => $data['full_name'] ?? '',
                'email' => $data['email'] ?? '',
                'role' => $data['role'] ?? 'job_seeker',
            ]);
            $this->redirect('/register');
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
        $this->redirect('/dashboard');
    }

    public function login(): void
    {
        $data = $this->only(['email', 'password', 'role']);
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
            ]);
            $this->redirect('/login');
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
            ]);
            $this->redirect('/login');
        }

        $this->loginUser((int) $user['id']);
        $this->flash('success', 'Welcome back.');
        if ($role['name'] === 'admin') {
            $this->redirect('/admin/overview');
        } 
        else if ($role['name'] === 'job_seeker') {
            $this->redirect('/cv/templates');
        }
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
        $this->redirect('/login');
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
            'role' => $role['name'] ?? null,
        ];
    }

    private function authenticated(): bool
    {
        return isset($_SESSION['user']['id']);
    }
}
