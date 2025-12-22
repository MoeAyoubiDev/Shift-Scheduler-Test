<?php require __DIR__ . '/../partials/header.php'; ?>
<section class="card">
    <h2>Shift Requests</h2>
    <p class="muted">Review and prioritize next-week requests.</p>
    <table>
        <thead>
            <tr>
                <th>Employee</th>
                <th>Date</th>
                <th>Shift</th>
                <th>Importance</th>
                <th>Pattern</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($requests as $request): ?>
                <tr>
                    <td><?= htmlspecialchars($request['employee']) ?></td>
                    <td><?= htmlspecialchars($request['date']) ?></td>
                    <td><?= htmlspecialchars($request['shift']) ?></td>
                    <td><?= htmlspecialchars($request['importance']) ?></td>
                    <td><?= htmlspecialchars($request['pattern']) ?></td>
                    <td><span class="pill"><?= htmlspecialchars($request['status']) ?></span></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>
<?php require __DIR__ . '/../partials/footer.php'; ?>
