<?php require __DIR__ . '/../partials/header.php'; ?>
<section class="card">
    <h2>Weekly Schedule</h2>
    <p class="muted">Auto-generated assignments with manual overrides available.</p>
    <table>
        <thead>
            <tr>
                <th>Employee</th>
                <th>Role</th>
                <th>Pattern</th>
                <th>Mon</th>
                <th>Tue</th>
                <th>Wed</th>
                <th>Thu</th>
                <th>Fri</th>
                <th>Sat</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($schedule as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['employee']) ?></td>
                    <td><?= htmlspecialchars($row['role']) ?></td>
                    <td><?= htmlspecialchars($row['pattern']) ?></td>
                    <?php foreach ($row['week'] as $shift): ?>
                        <td><?= htmlspecialchars($shift) ?></td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>
<?php require __DIR__ . '/../partials/footer.php'; ?>
