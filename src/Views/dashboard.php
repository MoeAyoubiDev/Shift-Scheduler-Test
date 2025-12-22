<?php require __DIR__ . '/partials/header.php'; ?>
<section class="welcome">
    <div>
        <h2>Welcome back, <?= htmlspecialchars($user['name']) ?></h2>
        <p class="muted">Role: <?= htmlspecialchars($user['role']) ?> · Section: <?= htmlspecialchars($user['section']) ?></p>
    </div>
    <div class="badge">Live Ops</div>
</section>

<section class="grid metrics">
    <article class="card">
        <h3>Coverage</h3>
        <p class="metric-value"><?= $metrics['coverage'] ?>%</p>
        <span class="muted">Weekly coverage ratio</span>
    </article>
    <article class="card">
        <h3>Pending requests</h3>
        <p class="metric-value"><?= $metrics['pending_requests'] ?></p>
        <span class="muted">Awaiting review</span>
    </article>
    <article class="card">
        <h3>On-time breaks</h3>
        <p class="metric-value"><?= $metrics['on_time_breaks'] ?>%</p>
        <span class="muted">Compliance rate</span>
    </article>
    <article class="card">
        <h3>Late breaks</h3>
        <p class="metric-value"><?= $metrics['late_breaks'] ?>%</p>
        <span class="muted">Requires attention</span>
    </article>
</section>

<section class="card">
    <h3>Weekly coverage preview</h3>
    <table>
        <thead>
            <tr>
                <th>Day</th>
                <th>AM</th>
                <th>MID</th>
                <th>PM</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($schedule as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['day']) ?></td>
                    <td><?= $row['am'] ?></td>
                    <td><?= $row['mid'] ?></td>
                    <td><?= $row['pm'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
