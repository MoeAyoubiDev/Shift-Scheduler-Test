<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Models\Section;
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

        $sections = [];
        $selectedSection = $user['section'];
        if ($user['role'] === 'director') {
            $sections = Section::all();
            if (!empty($sections)) {
                $selectedSection = $this->request->query['section'] ?? $sections[0]['name'];
            }
        }
        $schedule = Schedule::forSection($selectedSection);
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        View::render('schedule/index', [
            'user' => $user,
            'schedule' => $schedule,
            'sections' => $sections,
            'selectedSection' => $selectedSection,
            'flash' => $flash,
        ]);
    }

    public function generate(): void
    {
        $user = $_SESSION['user'] ?? null;
        if (!$user) {
            $this->response->redirect('/login');
            return;
        }

        if (!in_array($user['role'], ['director', 'team_leader', 'supervisor'], true)) {
            $this->response->redirect('/schedule');
            return;
        }

        $weekStartInput = $this->request->post['week_start'] ?? date('Y-m-d');
        $weekStart = (new \DateTimeImmutable($weekStartInput))
            ->modify('monday this week')
            ->format('Y-m-d');
        $section = $user['section'];
        if ($user['role'] === 'director') {
            $section = $this->request->post['section'] ?? $section;
        }

        Schedule::generate($section, $weekStart, $user['id']);
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Schedule draft generated.'];
        $this->response->redirect('/schedule');
    }
}
