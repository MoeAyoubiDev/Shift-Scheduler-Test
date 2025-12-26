<?php require __DIR__ . '/../partials/header.php'; ?>
<section class="page-header">
    <div>
        <p class="eyebrow">Director Dashboard</p>
        <h1>Welcome back, Director</h1>
        <p class="muted">Here’s what’s happening with your workforce today.</p>
    </div>
    <div class="page-actions">
        <a class="button ghost" href="/setup/company">Workspace setup</a>
    </div>
</section>

<?php if (!empty($flash)): ?>
    <div class="alert <?= htmlspecialchars($flash['type']) ?>">
        <?= htmlspecialchars($flash['message']) ?>
    </div>
<?php endif; ?>

<section class="grid metrics">
    <article class="card glass-card metric-card">
        <h3>Total Employees</h3>
        <p class="metric-value"><?= $summary['total_employees'] ?></p>
        <span class="muted">Active team members</span>
    </article>
    <article class="card glass-card metric-card">
        <h3>Labor Cost</h3>
        <p class="metric-value">$<?= number_format($summary['labor_cost']) ?></p>
        <span class="muted">Projected weekly</span>
    </article>
    <article class="card glass-card metric-card">
        <h3>Fill Rate</h3>
        <p class="metric-value"><?= $summary['fill_rate'] ?>%</p>
        <span class="muted">Coverage progress</span>
    </article>
    <article class="card glass-card metric-card">
        <h3>Open Shifts</h3>
        <p class="metric-value"><?= $summary['open_shifts'] ?></p>
        <span class="muted">Needs assignment</span>
    </article>
</section>

<section class="card glass-card">
    <h2>Quick Access</h2>
    <div class="grid quick-access">
        <a class="card glass-card quick-card" href="/schedule">
            <h3>Scheduling</h3>
            <p class="muted">Manage shifts and assignments.</p>
        </a>
        <a class="card glass-card quick-card" href="/requests">
            <h3>Team Management</h3>
            <p class="muted">Employees, roles, and permissions.</p>
        </a>
        <a class="card glass-card quick-card" href="/analytics">
            <h3>Analytics</h3>
            <p class="muted">Reports and insights.</p>
        </a>
        <a class="card glass-card quick-card" href="/time-tracking">
            <h3>Time Tracking</h3>
            <p class="muted">Hours, overtime, attendance.</p>
        </a>
        <a class="card glass-card quick-card" href="/locations">
            <h3>Locations</h3>
            <p class="muted">Sites and departments.</p>
        </a>
        <a class="card glass-card quick-card" href="/settings">
            <h3>Settings</h3>
            <p class="muted">Company & system preferences.</p>
        </a>
    </div>
    <div class="dashboard-columns">
        <article class="card glass-card activity-card">
            <h3>Recent Activity</h3>
            <?php if ($summary['recent_activity']): ?>
                <ul class="activity-list">
                    <?php foreach ($summary['recent_activity'] as $activity): ?>
                        <li class="activity-item">
                            <span class="activity-title"><?= htmlspecialchars($activity['title']) ?></span>
                            <span class="activity-detail"><?= htmlspecialchars($activity['detail']) ?></span>
                            <span class="activity-time"><?= htmlspecialchars($activity['time']) ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p class="muted">No recent activity.</p>
            <?php endif; ?>
        </article>
        <div class="stacked-cards">
            <article class="card glass-card">
                <h3>System Health</h3>
                <div class="progress-group">
                    <div>
                        <span>Coverage Rate</span>
                        <strong><?= $summary['system_health']['coverage_rate'] ?>%</strong>
                    </div>
                    <progress value="<?= $summary['system_health']['coverage_rate'] ?>" max="100"></progress>
                </div>
                <div class="progress-group">
                    <div>
                        <span>Budget Used</span>
                        <strong><?= $summary['system_health']['budget_used'] ?>%</strong>
                    </div>
                    <progress value="<?= $summary['system_health']['budget_used'] ?>" max="100"></progress>
                </div>
                <div class="progress-group">
                    <div>
                        <span>Compliance Score</span>
                        <strong><?= $summary['system_health']['compliance'] ?>%</strong>
                    </div>
                    <progress value="<?= $summary['system_health']['compliance'] ?>" max="100"></progress>
                </div>
            </article>
            <article class="card glass-card">
                <h3>This Week</h3>
                <ul class="stat-list">
                    <li><span>Total Shifts</span><strong><?= $summary['this_week']['total_shifts'] ?></strong></li>
                    <li><span>Total Hours</span><strong><?= number_format($summary['this_week']['total_hours']) ?></strong></li>
                    <li><span>Overtime Hours</span><strong><?= $summary['this_week']['overtime_hours'] ?></strong></li>
                    <li><span>Call-outs</span><strong><?= $summary['this_week']['call_outs'] ?></strong></li>
                </ul>
            </article>
        </div>
    </div>
</section>
<?php require __DIR__ . '/../partials/footer.php'; ?>
