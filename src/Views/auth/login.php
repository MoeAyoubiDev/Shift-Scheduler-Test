<?php require __DIR__ . '/../partials/header.php'; ?>
<section class="auth-layout">
    <a class="back-link" href="/">&#8592; Back to home</a>
    <div class="card glass-card auth-card">
        <h1>Welcome Back</h1>
        <p class="muted">Sign in to your account.</p>
        <?php if (!empty($flash)): ?>
            <div class="alert <?= htmlspecialchars($flash['type']) ?>">
                <?= htmlspecialchars($flash['message']) ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
            <div class="alert error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="post" action="/login" class="form">
            <label>
                Email Address
                <input type="email" name="email" placeholder="you@company.com" required>
            </label>
            <label>
                Password
                <input type="password" name="password" required>
            </label>
            <div class="form-row">
                <label class="checkbox">
                    <input type="checkbox" name="remember">
                    Remember me
                </label>
                <a class="muted" href="/login">Forgot password?</a>
            </div>
            <button type="submit" class="button btn-primary">Sign In</button>
        </form>
        <div class="card glass-card light demo-card">
            <p class="muted">Demo Accounts:</p>
            <p class="muted">director@shift.test → Director Dashboard</p>
            <p class="muted">leader@app.test → Team Leader Dashboard</p>
            <p class="muted">employee@agent.test → Employee Dashboard</p>
        </div>
        <p class="muted">
            Don't have an account?
            <a href="/register">Sign up</a>
        </p>
    </div>
</section>
<?php require __DIR__ . '/../partials/footer.php'; ?>
