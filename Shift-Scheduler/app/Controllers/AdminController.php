<?php

declare(strict_types=1);

namespace App\Controllers;

class AdminController
{
    public function dashboard(): void
    {
        view('dashboard/admin');
    }
}
