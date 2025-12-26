<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Models\Company;
use App\Models\SchedulingPreference;
use App\Models\Section;
use App\Models\User;
use App\Models\WorkRule;

final class SetupController extends BaseController
{
    public function company(): void
    {
        $user = $this->requireDirector();
        if (!$user) {
            return;
        }

        $company = Company::find($user['company_id']);
        View::render('setup/company', [
            'user' => $user,
            'company' => $company,
        ]);
    }

    public function saveCompany(): void
    {
        $user = $this->requireDirector();
        if (!$user) {
            return;
        }

        $data = [
            'name' => trim($this->request->post['name'] ?? ''),
            'industry' => trim($this->request->post['industry'] ?? ''),
            'size' => (int) ($this->request->post['size'] ?? 0),
            'timezone' => trim($this->request->post['timezone'] ?? ''),
            'address' => trim($this->request->post['address'] ?? ''),
            'contact_email' => trim($this->request->post['contact_email'] ?? ''),
            'contact_phone' => trim($this->request->post['contact_phone'] ?? ''),
        ];

        Company::updateProfile($user['company_id'], $data);
        $this->response->redirect('/setup/work-rules');
    }

    public function workRules(): void
    {
        $user = $this->requireDirector();
        if (!$user) {
            return;
        }

        $rules = WorkRule::findByCompany($user['company_id']);
        View::render('setup/work-rules', [
            'user' => $user,
            'rules' => $rules,
        ]);
    }

    public function saveWorkRules(): void
    {
        $user = $this->requireDirector();
        if (!$user) {
            return;
        }

        WorkRule::upsert($user['company_id'], [
            'standard_shift_hours' => (int) ($this->request->post['standard_shift_hours'] ?? 8),
            'max_consecutive_days' => (int) ($this->request->post['max_consecutive_days'] ?? 6),
            'min_hours_between_shifts' => (int) ($this->request->post['min_hours_between_shifts'] ?? 12),
            'overtime_threshold' => (int) ($this->request->post['overtime_threshold'] ?? 40),
            'auto_overtime' => isset($this->request->post['auto_overtime']) ? 1 : 0,
            'enforce_rest' => isset($this->request->post['enforce_rest']) ? 1 : 0,
            'allow_shift_swapping' => isset($this->request->post['allow_shift_swapping']) ? 1 : 0,
        ]);

        $this->response->redirect('/setup/employees');
    }

    public function employees(): void
    {
        $user = $this->requireDirector();
        if (!$user) {
            return;
        }

        $employees = User::bySection($user['section']);
        $sections = Section::all();
        View::render('setup/employees', [
            'user' => $user,
            'employees' => $employees,
            'sections' => $sections,
        ]);
    }

    public function addEmployee(): void
    {
        $user = $this->requireDirector();
        if (!$user) {
            return;
        }

        $name = trim($this->request->post['name'] ?? '');
        $email = trim($this->request->post['email'] ?? '');
        $role = trim($this->request->post['role'] ?? 'employee');
        $sectionId = (int) ($this->request->post['section_id'] ?? 0);

        if ($name !== '' && $email !== '' && $sectionId > 0 && !User::emailExists($email)) {
            User::create($name, $email, 'password', $sectionId, $role, $user['company_id']);
        }

        $this->response->redirect('/setup/employees');
    }

    public function preferences(): void
    {
        $user = $this->requireDirector();
        if (!$user) {
            return;
        }

        $preferences = SchedulingPreference::findByCompany($user['company_id']);
        View::render('setup/preferences', [
            'user' => $user,
            'preferences' => $preferences,
        ]);
    }

    public function savePreferences(): void
    {
        $user = $this->requireDirector();
        if (!$user) {
            return;
        }

        SchedulingPreference::upsert($user['company_id'], [
            'default_view' => $this->request->post['default_view'] ?? 'Weekly',
            'week_start_day' => $this->request->post['week_start_day'] ?? 'Sunday',
            'lead_time_weeks' => (int) ($this->request->post['lead_time_weeks'] ?? 2),
            'send_notifications' => isset($this->request->post['send_notifications']) ? 1 : 0,
            'require_confirmations' => isset($this->request->post['require_confirmations']) ? 1 : 0,
            'ai_scheduling' => isset($this->request->post['ai_scheduling']) ? 1 : 0,
        ]);

        $this->response->redirect('/setup/review');
    }

    public function review(): void
    {
        $user = $this->requireDirector();
        if (!$user) {
            return;
        }

        $company = Company::find($user['company_id']);
        $rules = WorkRule::findByCompany($user['company_id']);
        $preferences = SchedulingPreference::findByCompany($user['company_id']);

        View::render('setup/review', [
            'user' => $user,
            'company' => $company,
            'rules' => $rules,
            'preferences' => $preferences,
        ]);
    }

    public function complete(): void
    {
        $user = $this->requireDirector();
        if (!$user) {
            return;
        }

        Company::markSetupComplete($user['company_id']);
        $this->response->redirect('/dashboard');
    }

    private function requireDirector(): ?array
    {
        $user = $_SESSION['user'] ?? null;
        if (!$user || $user['role'] !== 'director') {
            $this->response->redirect('/login');
            return null;
        }

        return $user;
    }
}
