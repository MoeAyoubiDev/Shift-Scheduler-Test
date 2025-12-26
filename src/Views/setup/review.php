<?php require __DIR__ . '/../partials/header.php'; ?>
<section class="setup-page">
    <?php $step = 'review'; require __DIR__ . '/partials/stepper.php'; ?>

    <div class="card glass-card setup-card">
        <h1>Review &amp; Confirm</h1>
        <p class="muted">Everything looks good! Ready to start scheduling.</p>

        <div class="card glass-card light">
            <h3>Company Details</h3>
            <div class="review-grid">
                <div><span>Company Name</span><strong><?= htmlspecialchars($company['name'] ?? '') ?></strong></div>
                <div><span>Industry</span><strong><?= htmlspecialchars($company['industry'] ?? '') ?></strong></div>
                <div><span>Team Size</span><strong><?= htmlspecialchars((string) ($company['size'] ?? '')) ?></strong></div>
            </div>
        </div>

        <div class="card glass-card light">
            <h3>Work Rules</h3>
            <div class="review-grid">
                <div><span>Standard Shift</span><strong><?= htmlspecialchars((string) ($rules['standard_shift_hours'] ?? 8)) ?> hours</strong></div>
                <div><span>Overtime Threshold</span><strong><?= htmlspecialchars((string) ($rules['overtime_threshold'] ?? 40)) ?> hours/week</strong></div>
                <div><span>Minimum Rest</span><strong><?= htmlspecialchars((string) ($rules['min_hours_between_shifts'] ?? 12)) ?> hours</strong></div>
            </div>
        </div>

        <div class="card glass-card light">
            <h3>Scheduling Preferences</h3>
            <div class="review-grid">
                <div><span>Default View</span><strong><?= htmlspecialchars($preferences['default_view'] ?? 'Weekly') ?></strong></div>
                <div><span>Week Starts</span><strong><?= htmlspecialchars($preferences['week_start_day'] ?? 'Sunday') ?></strong></div>
                <div><span>Lead Time</span><strong><?= htmlspecialchars((string) ($preferences['lead_time_weeks'] ?? 2)) ?> weeks</strong></div>
            </div>
        </div>

        <div class="card glass-card light success-callout">
            <strong>Setup Complete!</strong>
            <span>Your workspace is ready. You can modify these settings later from your dashboard.</span>
        </div>

        <div class="form-actions">
            <a class="button ghost" href="/setup/preferences">Previous</a>
            <form method="post" action="/setup/complete" class="inline-form">
                <button type="submit" class="button btn-primary">Complete Setup</button>
            </form>
        </div>
    </div>
    <div class="setup-footer muted">5 of 5</div>
</section>
<?php require __DIR__ . '/../partials/footer.php'; ?>
