<?php

declare(strict_types=1);

namespace App\Controllers;

class DirectorController
{
    public function dashboard(): void
    {
        view('director/dashboard');
    }

    public function chooseSection(): void
    {
        view('director/choose-section');
    }
}
