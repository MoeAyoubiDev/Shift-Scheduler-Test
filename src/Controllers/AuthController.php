<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Models\Company;
use App\Models\SchedulingPreference;
use App\Models\User;
use App\Models\WorkRule;

final class AuthController extends BaseController
{
    public function showLogin(): void
    {
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        View::render('auth/login', ['flash' => $flash]);
    }

    public function showRegister(): void
    {
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        View::render('auth/register', [
            'flash' => $flash,
        ]);
    }

    public function login(): void
    {
        $email = trim($this->request->post['email'] ?? '');
        $password = trim($this->request->post['password'] ?? '');

        $user = User::attempt($email, $password);
        if (!$user) {
            View::render('auth/login', ['error' => 'Invalid credentials.']);
            return;
        }

        $_SESSION['user'] = $user;
        $this->response->redirect('/dashboard');
    }

    public function register(): void
    {
        $companyName = trim($this->request->post['company_name'] ?? '');
        $name = trim($this->request->post['name'] ?? '');
        $email = trim($this->request->post['email'] ?? '');
        $password = $this->request->post['password'] ?? '';
        $passwordConfirm = $this->request->post['password_confirm'] ?? '';

        $validationError = $this->validateRegistration($companyName, $name, $email, $password, $passwordConfirm);
        if ($validationError) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => $validationError];
            $this->response->redirect('/register');
            return;
        }

        $companyId = Company::create($companyName, $email);
        WorkRule::upsert($companyId, [
            'standard_shift_hours' => 8,
            'max_consecutive_days' => 6,
            'min_hours_between_shifts' => 12,
            'overtime_threshold' => 40,
            'auto_overtime' => 1,
            'enforce_rest' => 1,
            'allow_shift_swapping' => 1,
        ]);
        SchedulingPreference::upsert($companyId, [
            'default_view' => 'Weekly',
            'week_start_day' => 'Sunday',
            'lead_time_weeks' => 2,
            'send_notifications' => 1,
            'require_confirmations' => 1,
            'ai_scheduling' => 0,
        ]);

        $user = User::create($name, $email, $password, null, 'director', $companyId);
        $_SESSION['user'] = $user;
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Account created successfully. Let’s set up your workspace.'];
        $this->response->redirect('/setup/company');
    }

    public function logout(): void
    {
        session_destroy();
        $this->response->redirect('/login');
    }

    private function validateRegistration(
        string $companyName,
        string $name,
        string $email,
        string $password,
        string $passwordConfirm
    ): ?string {
        if ($companyName === '' || $name === '' || $email === '' || $password === '' || $passwordConfirm === '') {
            return 'Please complete all required fields.';
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return 'Please enter a valid email address.';
        }

        if (mb_strlen($password) < 8) {
            return 'Password must be at least 8 characters.';
        }

        if ($password !== $passwordConfirm) {
            return 'Passwords do not match.';
        }

        if (User::emailExists($email)) {
            return 'An account with that email already exists.';
        }

        return null;
    }
}
