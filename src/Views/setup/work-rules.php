<?php require __DIR__ . '/../partials/header.php'; ?>
<section class="setup-page">
    <?php $step = 'work-rules'; require __DIR__ . '/partials/stepper.php'; ?>

    <div class="card glass-card setup-card">
        <h1>Work Rules</h1>
        <p class="muted">Configure your scheduling policies.</p>

        <form method="post" action="/setup/work-rules" class="form">
            <div class="split">
                <label>
                    Standard Shift Duration
                    <input type="number" name="standard_shift_hours" value="<?= htmlspecialchars((string) ($rules['standard_shift_hours'] ?? 8)) ?>">
                </label>
                <label>
                    Maximum Consecutive Days
                    <input type="number" name="max_consecutive_days" value="<?= htmlspecialchars((string) ($rules['max_consecutive_days'] ?? 6)) ?>">
                </label>
            </div>
            <div class="split">
                <label>
                    Minimum Hours Between Shifts
                    <input type="number" name="min_hours_between_shifts" value="<?= htmlspecialchars((string) ($rules['min_hours_between_shifts'] ?? 12)) ?>">
                </label>
                <label>
                    Overtime Threshold (hours/week)
                    <input type="number" name="overtime_threshold" value="<?= htmlspecialchars((string) ($rules['overtime_threshold'] ?? 40)) ?>">
                </label>
            </div>
            <label class="checkbox">
                <input type="checkbox" name="auto_overtime" <?= !empty($rules['auto_overtime']) ? 'checked' : '' ?>>
                Enable automatic overtime calculations
            </label>
            <label class="checkbox">
                <input type="checkbox" name="enforce_rest" <?= !empty($rules['enforce_rest']) ? 'checked' : '' ?>>
                Enforce minimum rest periods
            </label>
            <label class="checkbox">
                <input type="checkbox" name="allow_shift_swapping" <?= !empty($rules['allow_shift_swapping']) ? 'checked' : '' ?>>
                Allow shift swapping with approval
            </label>
            <div class="form-actions">
                <a class="button ghost" href="/setup/company">Previous</a>
                <button type="submit" class="button btn-primary">Next</button>
            </div>
        </form>
    </div>
    <div class="setup-footer muted">2 of 5</div>
</section>
<?php require __DIR__ . '/../partials/footer.php'; ?>
