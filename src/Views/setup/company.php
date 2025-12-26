<?php require __DIR__ . '/../partials/header.php'; ?>
<section class="setup-page">
    <?php $step = 'company'; require __DIR__ . '/partials/stepper.php'; ?>

    <div class="card glass-card setup-card">
        <h1>Company Details</h1>
        <p class="muted">Tell us about your organization.</p>

        <form method="post" action="/setup/company" class="form">
            <div class="split">
                <label>
                    Company Name
                    <input type="text" name="name" value="<?= htmlspecialchars($company['name'] ?? '') ?>" required>
                </label>
                <label>
                    Industry
                    <input type="text" name="industry" value="<?= htmlspecialchars($company['industry'] ?? '') ?>">
                </label>
            </div>
            <div class="split">
                <label>
                    Company Size
                    <input type="number" name="size" value="<?= htmlspecialchars((string) ($company['size'] ?? '')) ?>">
                </label>
                <label>
                    Time Zone
                    <input type="text" name="timezone" value="<?= htmlspecialchars($company['timezone'] ?? '') ?>">
                </label>
            </div>
            <label>
                Company Address
                <input type="text" name="address" value="<?= htmlspecialchars($company['address'] ?? '') ?>">
            </label>
            <div class="split">
                <label>
                    Contact Email
                    <input type="email" name="contact_email" value="<?= htmlspecialchars($company['contact_email'] ?? '') ?>">
                </label>
                <label>
                    Contact Phone
                    <input type="text" name="contact_phone" value="<?= htmlspecialchars($company['contact_phone'] ?? '') ?>">
                </label>
            </div>
            <div class="form-actions">
                <a class="button ghost" href="/">Back to home</a>
                <button type="submit" class="button btn-primary">Next</button>
            </div>
        </form>
    </div>
    <div class="setup-footer muted">1 of 5</div>
</section>
<?php require __DIR__ . '/../partials/footer.php'; ?>
