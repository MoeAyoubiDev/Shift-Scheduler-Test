<?php require __DIR__ . '/../partials/header.php'; ?>
<section class="page-header">
    <div>
        <p class="eyebrow">Analytics</p>
        <h1>Analytics &amp; Reports</h1>
        <p class="muted">Review workforce metrics and performance trends.</p>
    </div>
</section>

<section class="grid metrics">
    <article class="card glass-card metric-card">
        <h3>Coverage Rate</h3>
        <p class="metric-value"><?= $metrics['coverage'] ?>%</p>
        <span class="muted">Weekly coverage ratio</span>
    </article>
    <article class="card glass-card metric-card">
        <h3>Pending Requests</h3>
        <p class="metric-value"><?= $metrics['pending_requests'] ?></p>
        <span class="muted">Awaiting review</span>
    </article>
    <article class="card glass-card metric-card">
        <h3>On-time Breaks</h3>
        <p class="metric-value"><?= $metrics['on_time_breaks'] ?>%</p>
        <span class="muted">Compliance rate</span>
    </article>
    <article class="card glass-card metric-card">
        <h3>Late Breaks</h3>
        <p class="metric-value"><?= $metrics['late_breaks'] ?>%</p>
        <span class="muted">Requires attention</span>
    </article>
</section>

<section class="card glass-card">
    <h2>Insights</h2>
    <p class="muted">Use these analytics to refine coverage, reduce overtime, and improve compliance.</p>
    <ul class="list">
        <li>
            <div>
                <strong>Coverage trend</strong>
                <span class="muted">Stable staffing levels across the week.</span>
            </div>
            <span class="pill">Stable</span>
        </li>
        <li>
            <div>
                <strong>Compliance score</strong>
                <span class="muted">Break adherence remains above target.</span>
            </div>
            <span class="pill"><?= $metrics['on_time_breaks'] ?>%</span>
        </li>
        <li>
            <div>
                <strong>Overtime risk</strong>
                <span class="muted"><?= $metrics['overtime_risk'] ?> teams flagged for overtime risk.</span>
            </div>
            <span class="pill">Monitor</span>
        </li>
    </ul>
</section>
<?php require __DIR__ . '/../partials/footer.php'; ?>
