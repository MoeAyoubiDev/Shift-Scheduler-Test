<?php require __DIR__ . '/../partials/header.php'; ?>
<section class="page-header">
    <div>
        <p class="eyebrow">Settings</p>
        <h1>Company Settings</h1>
        <p class="muted">Manage company profile and scheduling configurations.</p>
    </div>
</section>

<section class="grid two-column">
    <article class="card glass-card">
        <h2>Company Profile</h2>
        <?php if ($company): ?>
            <ul class="list">
                <li><span>Name</span><strong><?= htmlspecialchars($company['name']) ?></strong></li>
                <li><span>Industry</span><strong><?= htmlspecialchars($company['industry'] ?? '—') ?></strong></li>
                <li><span>Team Size</span><strong><?= htmlspecialchars((string) ($company['size'] ?? '—')) ?></strong></li>
                <li><span>Time Zone</span><strong><?= htmlspecialchars($company['timezone'] ?? '—') ?></strong></li>
                <li><span>Contact Email</span><strong><?= htmlspecialchars($company['contact_email'] ?? '—') ?></strong></li>
            </ul>
        <?php else: ?>
            <p class="muted">Company details are not configured yet.</p>
        <?php endif; ?>
        <a class="button ghost" href="/setup/company">Edit Company Details</a>
    </article>
    <article class="card glass-card">
        <h2>Work Rules</h2>
        <?php if ($rules): ?>
            <ul class="list">
                <li><span>Standard Shift</span><strong><?= htmlspecialchars((string) $rules['standard_shift_hours']) ?> hours</strong></li>
                <li><span>Overtime Threshold</span><strong><?= htmlspecialchars((string) $rules['overtime_threshold']) ?> hours/week</strong></li>
                <li><span>Minimum Rest</span><strong><?= htmlspecialchars((string) $rules['min_hours_between_shifts']) ?> hours</strong></li>
            </ul>
        <?php else: ?>
            <p class="muted">Work rules are not configured.</p>
        <?php endif; ?>
        <a class="button ghost" href="/setup/work-rules">Update Work Rules</a>
    </article>
</section>

<section class="grid two-column">
    <article class="card glass-card">
        <h2>Scheduling Preferences</h2>
        <?php if ($preferences): ?>
            <ul class="list">
                <li><span>Default View</span><strong><?= htmlspecialchars($preferences['default_view']) ?></strong></li>
                <li><span>Week Starts</span><strong><?= htmlspecialchars($preferences['week_start_day']) ?></strong></li>
                <li><span>Lead Time</span><strong><?= htmlspecialchars((string) $preferences['lead_time_weeks']) ?> weeks</strong></li>
            </ul>
        <?php else: ?>
            <p class="muted">Preferences are not configured.</p>
        <?php endif; ?>
        <a class="button ghost" href="/setup/preferences">Update Preferences</a>
    </article>
    <article class="card glass-card">
        <h2>Leadership Directory</h2>
        <?php if ($leaders): ?>
            <ul class="list">
                <?php foreach ($leaders as $leader): ?>
                    <li>
                        <div>
                            <strong><?= htmlspecialchars($leader['name']) ?></strong>
                            <span class="muted"><?= htmlspecialchars($leader['role']) ?></span>
                        </div>
                        <span class="pill"><?= htmlspecialchars($leader['section'] ?? 'All Sections') ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="muted">No leaders available.</p>
        <?php endif; ?>
    </article>
</section>
<?php require __DIR__ . '/../partials/footer.php'; ?>
