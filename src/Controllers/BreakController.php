<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\BreakLog;

final class BreakController extends BaseController
{
    public function log(): void
    {
        $user = $_SESSION['user'] ?? null;
        if (!$user) {
            $this->response->redirect('/login');
            return;
        }

        if ($user['role'] !== 'employee') {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Only employees can log breaks.'];
            $this->response->redirect('/dashboard');
            return;
        }

        $shiftDate = $this->request->post['shift_date'] ?? date('Y-m-d');
        $breakStart = $this->request->post['break_start'] ?: null;
        $breakEnd = $this->request->post['break_end'] ?: null;
        $delay = (int) ($this->request->post['delay_minutes'] ?? 0);
        $breakType = $this->request->post['break_type'] ?? 'REGULAR';
        if (!in_array($breakType, ['REGULAR', 'LUNCH', 'EMERGENCY'], true)) {
            $breakType = 'REGULAR';
        }

        BreakLog::record($user['id'], $shiftDate, $breakStart, $breakEnd, $delay, $breakType);
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Break log recorded.'];
        $this->response->redirect('/dashboard');
    }
}
