<?php require __DIR__ . '/../partials/header.php'; ?>
<section class="page-header">
    <div>
        <p class="eyebrow">Team Overview</p>
        <h1>Team Overview</h1>
        <p class="muted">Manage your team’s schedule and requests.</p>
    </div>
    <div class="page-actions">
        <a class="button ghost" href="/schedule">View Schedule</a>
    </div>
</section>

<?php if (!empty($flash)): ?>
    <div class="alert <?= htmlspecialchars($flash['type']) ?>">
        <?= htmlspecialchars($flash['message']) ?>
    </div>
<?php endif; ?>

<section class="grid metrics">
    <article class="card glass-card metric-card">
        <h3>Team Members</h3>
        <p class="metric-value"><?= $overview['team_members'] ?></p>
        <span class="muted">Active staff</span>
    </article>
    <article class="card glass-card metric-card">
        <h3>On Shift Now</h3>
        <p class="metric-value"><?= $overview['on_shift'] ?></p>
        <span class="muted">Currently working</span>
    </article>
    <article class="card glass-card metric-card">
        <h3>Pending Requests</h3>
        <p class="metric-value"><?= $overview['pending_requests'] ?></p>
        <span class="muted">Awaiting approval</span>
    </article>
    <article class="card glass-card metric-card">
        <h3>Coverage Rate</h3>
        <p class="metric-value"><?= $overview['coverage_rate'] ?>%</p>
        <span class="muted">This week</span>
    </article>
</section>

<section class="card glass-card">
    <h2>Quick Actions</h2>
    <div class="grid quick-access">
        <a class="card glass-card quick-card" href="/schedule">
            <h3>Create Shift</h3>
            <p class="muted">Build upcoming schedules.</p>
        </a>
        <a class="card glass-card quick-card" href="/requests">
            <h3>Assign Employee</h3>
            <p class="muted">Review and approve shifts.</p>
        </a>
        <a class="card glass-card quick-card" href="/requests">
            <h3>View Requests</h3>
            <p class="muted">Handle pending approvals.</p>
        </a>
        <a class="card glass-card quick-card" href="/time-tracking">
            <h3>Time Off</h3>
            <p class="muted">Track attendance updates.</p>
        </a>
    </div>
</section>

<section class="dashboard-columns">
    <article class="card glass-card">
        <div class="card-header">
            <h3>Upcoming Shifts</h3>
            <a class="button ghost small" href="/schedule">View All</a>
        </div>
        <?php if ($overview['upcoming_shifts']): ?>
            <ul class="list">
                <?php foreach ($overview['upcoming_shifts'] as $shift): ?>
                    <li>
                        <div>
                            <strong><?= htmlspecialchars($shift['employee']) ?></strong>
                            <span class="muted"><?= htmlspecialchars($shift['shift_date']) ?></span>
                        </div>
                        <span class="pill"><?= htmlspecialchars($shift['shift_type']) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="muted">No upcoming shifts scheduled.</p>
        <?php endif; ?>
    </article>
    <article class="card glass-card">
        <h3>Pending Requests</h3>
        <?php if ($overview['pending_queue']): ?>
            <ul class="list">
                <?php foreach ($overview['pending_queue'] as $request): ?>
                    <li>
                        <div>
                            <strong><?= htmlspecialchars($request['employee']) ?></strong>
                            <span class="muted"><?= htmlspecialchars($request['date']) ?></span>
                        </div>
                        <div class="pill-group">
                            <span class="pill"><?= htmlspecialchars($request['importance']) ?></span>
                            <form method="post" action="/requests/update" class="inline-form">
                                <input type="hidden" name="request_id" value="<?= (int) $request['id'] ?>">
                                <button type="submit" name="status" value="APPROVED" class="button btn-primary small">Approve</button>
                                <button type="submit" name="status" value="DECLINED" class="button secondary btn-secondary small">Deny</button>
                            </form>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
            <a class="button ghost small" href="/requests">View All Requests</a>
        <?php else: ?>
            <p class="muted">No pending requests right now.</p>
        <?php endif; ?>
    </article>
</section>

<section class="dashboard-columns">
    <article class="card glass-card">
        <div class="card-header">
            <h3>Team Members</h3>
            <a class="button ghost small" href="/requests">Manage Team</a>
        </div>
        <?php if ($overview['team_statuses']): ?>
            <ul class="list">
                <?php foreach ($overview['team_statuses'] as $member): ?>
                    <li>
                        <div>
                            <strong><?= htmlspecialchars($member['name']) ?></strong>
                            <span class="muted"><?= htmlspecialchars(ucfirst($member['role'])) ?></span>
                        </div>
                        <span class="pill status-<?= htmlspecialchars($member['status']) ?>">
                            <?= htmlspecialchars(str_replace('-', ' ', $member['status'])) ?>
                        </span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="muted">No team members found.</p>
        <?php endif; ?>
    </article>
    <div class="stacked-cards">
        <article class="card glass-card">
            <h3>Team Performance</h3>
            <div class="progress-group">
                <div>
                    <span>Attendance</span>
                    <strong><?= $overview['performance']['attendance'] ?>%</strong>
                </div>
                <progress value="<?= $overview['performance']['attendance'] ?>" max="100"></progress>
            </div>
            <div class="progress-group">
                <div>
                    <span>On-time Clock-ins</span>
                    <strong><?= $overview['performance']['on_time'] ?>%</strong>
                </div>
                <progress value="<?= $overview['performance']['on_time'] ?>" max="100"></progress>
            </div>
            <div class="progress-group">
                <div>
                    <span>Shift Coverage</span>
                    <strong><?= $overview['performance']['coverage'] ?>%</strong>
                </div>
                <progress value="<?= $overview['performance']['coverage'] ?>" max="100"></progress>
            </div>
        </article>
        <article class="card glass-card">
            <h3>Alerts</h3>
            <?php foreach ($overview['alerts'] as $alert): ?>
                <div class="alert-card <?= htmlspecialchars($alert['type']) ?>">
                    <strong><?= htmlspecialchars($alert['title']) ?></strong>
                    <span><?= htmlspecialchars($alert['detail']) ?></span>
                </div>
            <?php endforeach; ?>
        </article>
    </div>
</section>
<?php require __DIR__ . '/../partials/footer.php'; ?>
