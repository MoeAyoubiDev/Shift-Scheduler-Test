<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Models\Schedule;

final class ScheduleController extends BaseController
{
    public function index(): void
    {
        $user = $_SESSION['user'] ?? null;
        if (!$user) {
            $this->response->redirect('/login');
            return;
        }

        $schedule = Schedule::forSection($user['section']);

        View::render('schedule/index', [
            'user' => $user,
            'schedule' => $schedule,
        ]);
    }
}
