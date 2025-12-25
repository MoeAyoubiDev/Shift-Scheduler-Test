<?php require __DIR__ . '/../partials/header.php'; ?>
<section class="card glass-card">
    <h2>Shift Requests</h2>
    <p class="muted">Submit new requests or review incoming submissions.</p>

    <?php if (!empty($flash)): ?>
        <div class="alert <?= htmlspecialchars($flash['type']) ?>">
            <?= htmlspecialchars($flash['message']) ?>
        </div>
    <?php endif; ?>

    <div class="grid two-column">
        <form method="post" action="/requests/submit" class="form glass-card form-card">
            <h3>Submit a request</h3>
            <label>
                Requested date
                <input type="date" name="requested_date" required>
            </label>
            <label>
                Shift type
                <select name="shift_type" required>
                    <option value="AM">AM</option>
                    <option value="MID">MID</option>
                    <option value="PM">PM</option>
                </select>
            </label>
            <label>
                Importance
                <select name="importance" required>
                    <option value="LOW">LOW</option>
                    <option value="NORMAL" selected>NORMAL</option>
                    <option value="HIGH">HIGH</option>
                </select>
            </label>
            <label>
                Pattern
                <select name="pattern" required>
                    <option value="5x2">5x2</option>
                    <option value="6x1">6x1</option>
                </select>
            </label>
            <label>
                Reason (optional)
                <input type="text" name="reason" placeholder="Optional request reason">
            </label>
            <button type="submit" class="button btn-primary">Submit request</button>
        </form>

        <div class="glass-card">
            <h3>Approval queue</h3>
            <div class="table-wrap">
                <table class="table-glass">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Date</th>
                            <th>Shift</th>
                            <th>Importance</th>
                            <th>Pattern</th>
                            <th>Status</th>
                            <?php if (in_array($user['role'], ['director', 'team_leader', 'supervisor'], true)): ?>
                                <th>Action</th>
                            <?php endif; ?>
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
                                <?php if (in_array($user['role'], ['director', 'team_leader', 'supervisor'], true)): ?>
                                    <td>
                                        <form method="post" action="/requests/update" class="inline-form">
                                            <input type="hidden" name="request_id" value="<?= (int) $request['id'] ?>">
                                            <button type="submit" name="status" value="Approved" class="button btn-primary small">Approve</button>
                                            <button type="submit" name="status" value="Declined" class="button secondary btn-secondary small">Decline</button>
                                        </form>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
<?php require __DIR__ . '/../partials/footer.php'; ?>
