<?php require __DIR__ . '/../partials/header.php'; ?>
<section class="auth-layout">
    <a class="back-link" href="/">&#8592; Back to home</a>
    <div class="card glass-card auth-card">
        <h1>Create Account</h1>
        <p class="muted">Start your 14-day free trial.</p>
        <?php if (!empty($flash)): ?>
            <div class="alert <?= htmlspecialchars($flash['type']) ?>">
                <?= htmlspecialchars($flash['message']) ?>
            </div>
        <?php endif; ?>
        <form method="post" action="/register" class="form">
            <label>
                Company Name
                <input type="text" name="company_name" placeholder="Acme Corporation" required>
            </label>
            <label>
                Full Name
                <input type="text" name="name" placeholder="John Doe" required>
            </label>
            <label>
                Work Email
                <input type="email" name="email" placeholder="you@company.com" required>
            </label>
            <label>
                Password
                <input type="password" name="password" required minlength="8">
            </label>
            <label>
                Confirm Password
                <input type="password" name="password_confirm" required minlength="8">
            </label>
            <label class="checkbox">
                <input type="checkbox" required>
                I agree to the <a href="/register">Terms of Service</a> and <a href="/register">Privacy Policy</a>
            </label>
            <button type="submit" class="button btn-primary">Create Account</button>
        </form>
        <p class="muted">
            Already have an account?
            <a href="/login">Sign in</a>
        </p>
        <div class="auth-footer muted">
            <span>No credit card</span>
            <span>14-day trial</span>
            <span>Cancel anytime</span>
        </div>
    </div>
</section>
<?php require __DIR__ . '/../partials/footer.php'; ?>
