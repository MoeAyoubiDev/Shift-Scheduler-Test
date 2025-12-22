<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Models\ShiftRequest;

final class RequestController extends BaseController
{
    public function index(): void
    {
        $user = $_SESSION['user'] ?? null;
        if (!$user) {
            $this->response->redirect('/login');
            return;
        }

        $requests = ShiftRequest::forSection($user);
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        View::render('requests/index', [
            'user' => $user,
            'requests' => $requests,
            'flash' => $flash,
        ]);
    }

    public function submit(): void
    {
        $user = $_SESSION['user'] ?? null;
        if (!$user) {
            $this->response->redirect('/login');
            return;
        }

        $payload = [
            'requested_date' => $this->request->post['requested_date'] ?? '',
            'shift_type' => $this->request->post['shift_type'] ?? '',
            'importance' => $this->request->post['importance'] ?? '',
            'pattern' => $this->request->post['pattern'] ?? '',
            'reason' => trim($this->request->post['reason'] ?? ''),
        ];

        if ($payload['requested_date'] === '' || $payload['shift_type'] === '' || $payload['importance'] === '' || $payload['pattern'] === '') {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Please complete all required fields.'];
            $this->response->redirect('/requests');
            return;
        }

        ShiftRequest::create($user['id'], $payload);
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Shift request submitted.'];
        $this->response->redirect('/requests');
    }

    public function updateStatus(): void
    {
        $user = $_SESSION['user'] ?? null;
        if (!$user) {
            $this->response->redirect('/login');
            return;
        }

        if (!in_array($user['role'], ['director', 'team_leader', 'supervisor'], true)) {
            $this->response->redirect('/requests');
            return;
        }

        $requestId = (int) ($this->request->post['request_id'] ?? 0);
        $status = $this->request->post['status'] ?? '';

        if ($requestId && in_array($status, ['Approved', 'Declined'], true)) {
            ShiftRequest::updateStatus($requestId, $status);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Request status updated.'];
        }

        $this->response->redirect('/requests');
    }
}
