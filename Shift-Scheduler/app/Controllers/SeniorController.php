<?php

declare(strict_types=1);

namespace App\Controllers;

class SeniorController
{
    public function dashboard(): void
    {
        view('senior/dashboard');
    }
}
