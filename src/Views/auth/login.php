<?php require __DIR__ . '/../partials/header.php'; ?>
<section class="card form-card glass-card">
    <h2>Login</h2>
    <p class="muted">Use the demo credentials provided in the README.</p>
    <?php if (!empty($error)): ?>
        <div class="alert error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post" action="/login" class="form">
        <label>
            Email
            <input type="email" name="email" required>
        </label>
        <label>
            Password
            <input type="password" name="password" required>
        </label>
        <button type="submit" class="button btn-primary">Sign in</button>
    </form>
</section>
<?php require __DIR__ . '/../partials/footer.php'; ?>
