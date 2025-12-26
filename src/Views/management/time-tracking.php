<?php require __DIR__ . '/../partials/header.php'; ?>
<section class="page-header">
    <div>
        <p class="eyebrow">Time Tracking</p>
        <h1>Time Tracking</h1>
        <p class="muted">Monitor attendance, breaks, and overtime across your teams.</p>
    </div>
</section>

<section class="card glass-card">
    <h2>Time Tracking Overview</h2>
    <p class="muted">Review break compliance and attendance data in one place.</p>
    <div class="grid two-column">
        <div class="card glass-card light">
            <h3>Attendance Summary</h3>
            <p class="muted">Track attendance and call-outs.</p>
            <a class="button ghost" href="/dashboard">View Attendance</a>
        </div>
        <div class="card glass-card light">
            <h3>Break Compliance</h3>
            <p class="muted">Review break logs and delays.</p>
            <a class="button ghost" href="/dashboard">View Break Logs</a>
        </div>
    </div>
</section>
<?php require __DIR__ . '/../partials/footer.php'; ?>
