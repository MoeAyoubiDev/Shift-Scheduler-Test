<?php require __DIR__ . '/../partials/header.php'; ?>
<section class="setup-page">
    <?php $step = 'preferences'; require __DIR__ . '/partials/stepper.php'; ?>

    <div class="card glass-card setup-card">
        <h1>Scheduling Preferences</h1>
        <p class="muted">Customize how shifts are scheduled.</p>

        <form method="post" action="/setup/preferences" class="form">
            <label>
                Default Scheduling View
                <div class="pill-group">
                    <?php $defaultView = $preferences['default_view'] ?? 'Weekly'; ?>
                    <?php foreach (['Weekly', 'Bi-Weekly', 'Monthly'] as $view): ?>
                        <label class="pill-radio">
                            <input type="radio" name="default_view" value="<?= $view ?>" <?= $defaultView === $view ? 'checked' : '' ?>>
                            <span><?= $view ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </label>
            <div class="split">
                <label>
                    Week Start Day
                    <input type="text" name="week_start_day" value="<?= htmlspecialchars($preferences['week_start_day'] ?? 'Sunday') ?>">
                </label>
                <label>
                    Scheduling Lead Time (weeks)
                    <input type="number" name="lead_time_weeks" value="<?= htmlspecialchars((string) ($preferences['lead_time_weeks'] ?? 2)) ?>">
                </label>
            </div>
            <label class="checkbox">
                <input type="checkbox" name="send_notifications" <?= !empty($preferences['send_notifications']) ? 'checked' : '' ?>>
                Send schedule notifications
            </label>
            <label class="checkbox">
                <input type="checkbox" name="require_confirmations" <?= !empty($preferences['require_confirmations']) ? 'checked' : '' ?>>
                Require shift confirmations
            </label>
            <label class="checkbox">
                <input type="checkbox" name="ai_scheduling" <?= !empty($preferences['ai_scheduling']) ? 'checked' : '' ?>>
                Enable AI-powered scheduling
            </label>
            <div class="form-actions">
                <a class="button ghost" href="/setup/employees">Previous</a>
                <button type="submit" class="button btn-primary">Next</button>
            </div>
        </form>
    </div>
    <div class="setup-footer muted">4 of 5</div>
</section>
<?php require __DIR__ . '/../partials/footer.php'; ?>
