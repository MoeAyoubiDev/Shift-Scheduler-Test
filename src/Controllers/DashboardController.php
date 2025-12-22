<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Models\Metrics;
use App\Models\Schedule;

final class DashboardController extends BaseController
{
    public function home(): void
    {
        View::render('landing');
    }

    public function dashboard(): void
    {
        $user = $_SESSION['user'] ?? null;
        if (!$user) {
            $this->response->redirect('/login');
            return;
        }

        $metrics = Metrics::overview($user['section']);
        $schedule = Schedule::weekPreview($user['section']);

        View::render('dashboard', [
            'user' => $user,
            'metrics' => $metrics,
            'schedule' => $schedule,
        ]);
    }
}
