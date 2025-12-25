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
        <div class="glass-card form-card">
            <h3>Submit a request</h3>
            <?php if ($user['role'] !== 'employee'): ?>
                <p class="muted">Only employees can submit shift requests. Senior staff and leaders are read-only.</p>
            <?php else: ?>
                <form method="post" action="/requests/submit" class="form">
                    <label>
                        Requested date (next week)
                        <input type="date" name="requested_date" required>
                    </label>
                    <label>
                        Shift type
                        <select name="shift_type">
                            <option value="">Select shift</option>
                            <option value="AM">AM</option>
                            <option value="MID">MID</option>
                            <option value="PM">PM</option>
                            <option value="NIGHT">Night</option>
                            <option value="DEFAULT">Default</option>
                        </select>
                    </label>
                    <label>
                        <input type="checkbox" name="is_day_off" value="1">
                        Request day off instead
                    </label>
                    <label>
                        Importance
                        <select name="importance" required>
                            <option value="LOW">LOW</option>
                            <option value="MEDIUM" selected>MEDIUM</option>
                            <option value="HIGH">HIGH</option>
                        </select>
                    </label>
                    <label>
                        Schedule pattern (optional)
                        <select name="pattern">
                            <option value="">No preference</option>
                            <option value="5x2">5x2</option>
                            <option value="4x3">4x3</option>
                            <option value="ROTATING">Rotating</option>
                        </select>
                    </label>
                    <label>
                        Reason (min 10 characters)
                        <input type="text" name="reason" required minlength="10" placeholder="Explain your request">
                    </label>
                    <button type="submit" class="button btn-primary">Submit request</button>
                </form>
            <?php endif; ?>
        </div>

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
                            <?php if (in_array($user['role'], ['director', 'team_leader'], true)): ?>
                                <th>Action</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($requests as $request): ?>
                            <tr>
                                <td><?= htmlspecialchars($request['employee']) ?></td>
                                <td><?= htmlspecialchars($request['date']) ?></td>
                                <td><?= $request['is_day_off'] ? 'Day Off' : htmlspecialchars($request['shift'] ?? 'Default') ?></td>
                                <td><?= htmlspecialchars($request['importance']) ?></td>
                                <td><?= htmlspecialchars($request['pattern'] ?? '—') ?></td>
                                <td><span class="pill"><?= htmlspecialchars($request['status']) ?></span></td>
                                <?php if (in_array($user['role'], ['director', 'team_leader'], true)): ?>
                                    <td>
                                        <form method="post" action="/requests/update" class="inline-form">
                                            <input type="hidden" name="request_id" value="<?= (int) $request['id'] ?>">
                                            <button type="submit" name="status" value="APPROVED" class="button btn-primary small">Approve</button>
                                            <button type="submit" name="status" value="DECLINED" class="button secondary btn-secondary small">Decline</button>
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
