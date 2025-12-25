<?php

declare(strict_types=1);

namespace App\Controllers;

class EmployeeController
{
    public function dashboard(): void
    {
        view('employee/dashboard');
    }
}
