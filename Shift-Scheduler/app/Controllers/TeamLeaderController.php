<?php

declare(strict_types=1);

namespace App\Controllers;

class TeamLeaderController
{
    public function dashboard(): void
    {
        view('teamleader/dashboard');
    }
}
