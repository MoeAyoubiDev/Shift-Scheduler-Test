<?php require __DIR__ . '/partials/header.php'; ?>
<section class="welcome">
    <div>
        <h2>Welcome back, <?= htmlspecialchars($user['name']) ?></h2>
        <p class="muted">Role: <?= htmlspecialchars($user['role']) ?> · Section: <?= htmlspecialchars($user['section']) ?></p>
    </div>
    <div class="badge">Live Ops</div>
</section>

<?php if (!empty($flash)): ?>
    <div class="alert <?= htmlspecialchars($flash['type']) ?>">
        <?= htmlspecialchars($flash['message']) ?>
    </div>
<?php endif; ?>

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
    <?php if ($schedule): ?>
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
    <?php else: ?>
        <p class="muted">Generate a schedule to see coverage for the week.</p>
    <?php endif; ?>
</section>

<section class="grid two-column">
    <article class="card">
        <h3>Break compliance log</h3>
        <p class="muted">Track your most recent break submissions.</p>
        <?php if ($breaks): ?>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Start</th>
                        <th>End</th>
                        <th>Delay</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($breaks as $break): ?>
                        <tr>
                            <td><?= htmlspecialchars($break['shift_date']) ?></td>
                            <td><?= htmlspecialchars($break['break_start'] ?? '—') ?></td>
                            <td><?= htmlspecialchars($break['break_end'] ?? '—') ?></td>
                            <td><?= htmlspecialchars((string) $break['delay_minutes']) ?> min</td>
                            <td><span class="pill"><?= htmlspecialchars($break['status']) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="muted">No breaks logged yet.</p>
        <?php endif; ?>
    </article>
    <article class="card">
        <h3>Log a break</h3>
        <p class="muted">Capture break timing and delays for compliance tracking.</p>
        <form method="post" action="/breaks/log" class="form">
            <label>
                Shift date
                <input type="date" name="shift_date" value="<?= date('Y-m-d') ?>" required>
            </label>
            <div class="split">
                <label>
                    Break start
                    <input type="datetime-local" name="break_start">
                </label>
                <label>
                    Break end
                    <input type="datetime-local" name="break_end">
                </label>
            </div>
            <label>
                Delay minutes
                <input type="number" name="delay_minutes" min="0" max="60" value="0" required>
            </label>
            <button type="submit" class="button">Record break</button>
        </form>
    </article>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
