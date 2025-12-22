<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Models\User;

final class AuthController extends BaseController
{
    public function showLogin(): void
    {
        View::render('auth/login');
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

    public function logout(): void
    {
        session_destroy();
        $this->response->redirect('/login');
    }
}
