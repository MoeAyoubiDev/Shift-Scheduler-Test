<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Models\Company;
use App\Models\Metrics;
use App\Models\SchedulingPreference;
use App\Models\Section;
use App\Models\User;
use App\Models\WorkRule;

final class ManagementController extends BaseController
{
    public function analytics(): void
    {
        $user = $_SESSION['user'] ?? null;
        if (!$user) {
            $this->response->redirect('/login');
            return;
        }

        $metrics = Metrics::overview($user['section']);
        View::render('management/analytics', [
            'user' => $user,
            'metrics' => $metrics,
        ]);
    }

    public function timeTracking(): void
    {
        $user = $_SESSION['user'] ?? null;
        if (!$user) {
            $this->response->redirect('/login');
            return;
        }

        View::render('management/time-tracking', [
            'user' => $user,
        ]);
    }

    public function locations(): void
    {
        $user = $_SESSION['user'] ?? null;
        if (!$user) {
            $this->response->redirect('/login');
            return;
        }

        $sections = Section::all();
        View::render('management/locations', [
            'user' => $user,
            'sections' => $sections,
        ]);
    }

    public function settings(): void
    {
        $user = $_SESSION['user'] ?? null;
        if (!$user) {
            $this->response->redirect('/login');
            return;
        }

        $company = $user['company_id'] ? Company::find($user['company_id']) : null;
        $rules = $user['company_id'] ? WorkRule::findByCompany($user['company_id']) : null;
        $preferences = $user['company_id'] ? SchedulingPreference::findByCompany($user['company_id']) : null;
        $leaders = User::bySection($user['section']);

        View::render('management/settings', [
            'user' => $user,
            'company' => $company,
            'rules' => $rules,
            'preferences' => $preferences,
            'leaders' => $leaders,
        ]);
    }
}
