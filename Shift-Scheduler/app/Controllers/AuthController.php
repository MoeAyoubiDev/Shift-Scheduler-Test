<?php

declare(strict_types=1);

namespace App\Controllers;

class AuthController
{
    public function showLogin(): void
    {
        view('auth/login');
    }
}
