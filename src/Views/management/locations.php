<?php require __DIR__ . '/../partials/header.php'; ?>
<section class="page-header">
    <div>
        <p class="eyebrow">Locations</p>
        <h1>Locations &amp; Departments</h1>
        <p class="muted">Manage departments and staffing groups.</p>
    </div>
</section>

<section class="card glass-card">
    <h2>Active Departments</h2>
    <?php if ($sections): ?>
        <ul class="list">
            <?php foreach ($sections as $section): ?>
                <li>
                    <div>
                        <strong><?= htmlspecialchars($section['name']) ?></strong>
                        <span class="muted">Department</span>
                    </div>
                    <a class="button ghost small" href="/requests">Manage Team</a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p class="muted">No locations available.</p>
    <?php endif; ?>
</section>
<?php require __DIR__ . '/../partials/footer.php'; ?>
