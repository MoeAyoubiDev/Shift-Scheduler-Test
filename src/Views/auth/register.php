<?php require __DIR__ . '/../partials/header.php'; ?>
<section class="card form-card glass-card">
    <h2>Create your account</h2>
    <p class="muted">Register as an employee to access scheduling, requests, and break tracking.</p>
    <?php if (!empty($flash)): ?>
        <div class="alert <?= htmlspecialchars($flash['type']) ?>">
            <?= htmlspecialchars($flash['message']) ?>
        </div>
    <?php endif; ?>
    <form method="post" action="/register" class="form">
        <label>
            Full name
            <input type="text" name="name" required>
        </label>
        <label>
            Email
            <input type="email" name="email" required>
        </label>
        <label>
            Password
            <input type="password" name="password" required minlength="8">
        </label>
        <label>
            Confirm password
            <input type="password" name="password_confirm" required minlength="8">
        </label>
        <label>
            Section
            <select name="section_id" required>
                <option value="">Select section</option>
                <?php foreach ($sections as $section): ?>
                    <option value="<?= (int) $section['id'] ?>">
                        <?= htmlspecialchars($section['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <button type="submit" class="button btn-primary">Create account</button>
    </form>
    <p class="muted">
        Already have an account?
        <a href="/login">Sign in here</a>
    </p>
</section>
<?php require __DIR__ . '/../partials/footer.php'; ?>
