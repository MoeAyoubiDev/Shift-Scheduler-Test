<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Models\Section;
use App\Models\User;

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
        $sections = Section::all();
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        View::render('auth/register', [
            'sections' => $sections,
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
        $name = trim($this->request->post['name'] ?? '');
        $email = trim($this->request->post['email'] ?? '');
        $password = $this->request->post['password'] ?? '';
        $passwordConfirm = $this->request->post['password_confirm'] ?? '';
        $sectionId = (int) ($this->request->post['section_id'] ?? 0);

        $validationError = $this->validateRegistration($name, $email, $password, $passwordConfirm, $sectionId);
        if ($validationError) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => $validationError];
            $this->response->redirect('/register');
            return;
        }

        $user = User::create($name, $email, $password, $sectionId, 'employee');
        $_SESSION['user'] = $user;
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Account created successfully. Welcome aboard!'];
        $this->response->redirect('/dashboard');
    }

    public function logout(): void
    {
        session_destroy();
        $this->response->redirect('/login');
    }

    private function validateRegistration(
        string $name,
        string $email,
        string $password,
        string $passwordConfirm,
        int $sectionId
    ): ?string {
        if ($name === '' || $email === '' || $password === '' || $passwordConfirm === '') {
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

        if ($sectionId <= 0) {
            return 'Please select a section.';
        }

        if (!Section::exists($sectionId)) {
            return 'Selected section is invalid.';
        }

        if (User::emailExists($email)) {
            return 'An account with that email already exists.';
        }

        return null;
    }
}
