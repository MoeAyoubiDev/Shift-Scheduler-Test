<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Models\BreakLog;
use App\Models\DirectorOverview;
use App\Models\Metrics;
use App\Models\Schedule;
use App\Models\TeamOverview;

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

        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        if ($user['role'] === 'director') {
            $summary = DirectorOverview::summary();
            View::render('dashboard/director', [
                'user' => $user,
                'summary' => $summary,
                'flash' => $flash,
            ]);
            return;
        }

        if (in_array($user['role'], ['team_leader', 'supervisor'], true)) {
            $overview = TeamOverview::forLeader($user);
            View::render('dashboard/leader', [
                'user' => $user,
                'overview' => $overview,
                'flash' => $flash,
            ]);
            return;
        }

        $metrics = Metrics::overview($user['section']);
        $schedule = Schedule::weekPreview($user['section']);
        $breaks = BreakLog::recentForUser($user['id']);

        View::render('dashboard/employee', [
            'user' => $user,
            'metrics' => $metrics,
            'schedule' => $schedule,
            'breaks' => $breaks,
            'flash' => $flash,
        ]);
    }
}
