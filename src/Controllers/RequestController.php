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

        if ($user['role'] !== 'employee') {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Only employees can submit shift requests.'];
            $this->response->redirect('/requests');
            return;
        }

        $payload = [
            'requested_date' => $this->request->post['requested_date'] ?? '',
            'shift_type' => $this->request->post['shift_type'] ?? '',
            'importance' => $this->request->post['importance'] ?? '',
            'pattern' => $this->request->post['pattern'] ?? null,
            'is_day_off' => isset($this->request->post['is_day_off']) ? 1 : 0,
            'reason' => trim($this->request->post['reason'] ?? ''),
        ];

        $validationError = $this->validateRequestPayload($user, $payload);
        if ($validationError) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => $validationError];
            $this->response->redirect('/requests');
            return;
        }

        if ($payload['shift_type'] === '') {
            $payload['shift_type'] = null;
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

        if (!in_array($user['role'], ['director', 'team_leader'], true)) {
            $this->response->redirect('/requests');
            return;
        }

        $requestId = (int) ($this->request->post['request_id'] ?? 0);
        $status = $this->request->post['status'] ?? '';

        if ($requestId && in_array($status, ['APPROVED', 'DECLINED'], true)) {
            ShiftRequest::updateStatus($requestId, $status);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Request status updated.'];
        }

        $this->response->redirect('/requests');
    }

    private function validateRequestPayload(array $user, array $payload): ?string
    {
        if ($payload['requested_date'] === '' || $payload['importance'] === '') {
            return 'Please complete all required fields.';
        }

        if ($payload['is_day_off'] === 0 && $payload['shift_type'] === '') {
            return 'Please select a shift type or mark the request as day off.';
        }

        if ($payload['is_day_off'] === 0
            && !in_array($payload['shift_type'], ['AM', 'MID', 'PM', 'NIGHT', 'DEFAULT'], true)
        ) {
            return 'Select a valid shift type.';
        }

        if (!in_array($payload['importance'], ['LOW', 'MEDIUM', 'HIGH'], true)) {
            return 'Select a valid importance level.';
        }

        if ($payload['pattern'] !== null && $payload['pattern'] !== ''
            && !in_array($payload['pattern'], ['5x2', '4x3', 'ROTATING'], true)
        ) {
            return 'Select a valid schedule pattern.';
        }

        if (mb_strlen($payload['reason']) < 10) {
            return 'Reason must be at least 10 characters.';
        }

        $today = new \DateTimeImmutable('today');
        $currentWeekStart = $today->modify('monday this week');
        $currentWeekEnd = $currentWeekStart->modify('+6 days');
        $dayOfWeek = (int) $today->format('N');

        if ($dayOfWeek === 7) {
            return 'Submissions are not allowed on Sunday.';
        }

        if ($today < $currentWeekStart || $today > $currentWeekEnd) {
            return 'Submissions are only allowed during the current week.';
        }

        $requestedDate = \DateTimeImmutable::createFromFormat('Y-m-d', $payload['requested_date']);
        if (!$requestedDate) {
            return 'Please provide a valid request date.';
        }

        $nextWeekStart = $currentWeekStart->modify('+1 week');
        $nextWeekEnd = $nextWeekStart->modify('+6 days');

        if ($requestedDate < $nextWeekStart || $requestedDate > $nextWeekEnd) {
            return 'Requests must be scheduled for next week (Monday through Sunday).';
        }

        if (ShiftRequest::hasRequestForDate($user['id'], $payload['requested_date'])) {
            return 'You already have a request for that date.';
        }

        return null;
    }
}
