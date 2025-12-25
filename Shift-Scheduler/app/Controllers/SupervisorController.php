<?php

declare(strict_types=1);

namespace App\Controllers;

class SupervisorController
{
    public function dashboard(): void
    {
        view('supervisor/dashboard');
    }
}
