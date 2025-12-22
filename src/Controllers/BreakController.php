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

        $shiftDate = $this->request->post['shift_date'] ?? date('Y-m-d');
        $breakStart = $this->request->post['break_start'] ?: null;
        $breakEnd = $this->request->post['break_end'] ?: null;
        $delay = (int) ($this->request->post['delay_minutes'] ?? 0);

        BreakLog::record($user['id'], $shiftDate, $breakStart, $breakEnd, $delay);
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Break log recorded.'];
        $this->response->redirect('/dashboard');
    }
}
