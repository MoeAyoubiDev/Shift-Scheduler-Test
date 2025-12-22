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

        $requests = ShiftRequest::forSection($user['section']);

        View::render('requests/index', [
            'user' => $user,
            'requests' => $requests,
        ]);
    }
}
