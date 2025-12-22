<?php require __DIR__ . '/../partials/header.php'; ?>
<section class="card">
    <h2>Weekly Schedule</h2>
    <p class="muted">Generate new drafts based on approved requests and staffing patterns.</p>

    <?php if (!empty($flash)): ?>
        <div class="alert <?= htmlspecialchars($flash['type']) ?>">
            <?= htmlspecialchars($flash['message']) ?>
        </div>
    <?php endif; ?>

    <?php if ($user['role'] === 'director' && $sections): ?>
        <form method="get" action="/schedule" class="form schedule-form">
            <label>
                View section
                <select name="section" required>
                    <?php foreach ($sections as $section): ?>
                        <option value="<?= htmlspecialchars($section['name']) ?>" <?= $section['name'] === $selectedSection ? 'selected' : '' ?>>
                            <?= htmlspecialchars($section['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>
            <button type="submit" class="button secondary">Filter schedule</button>
        </form>
    <?php endif; ?>

    <?php if (in_array($user['role'], ['director', 'team_leader', 'supervisor'], true)): ?>
        <form method="post" action="/schedule/generate" class="form schedule-form">
            <label>
                Week start
                <input type="date" name="week_start" value="<?= date('Y-m-d', strtotime('monday this week')) ?>" required>
            </label>
            <?php if ($user['role'] === 'director'): ?>
                <label>
                    Section
                    <select name="section" required>
                        <?php foreach ($sections as $section): ?>
                            <option value="<?= htmlspecialchars($section['name']) ?>" <?= $section['name'] === $selectedSection ? 'selected' : '' ?>>
                                <?= htmlspecialchars($section['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>
            <?php endif; ?>
            <button type="submit" class="button">Generate schedule</button>
        </form>
    <?php endif; ?>

    <?php if ($schedule): ?>
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
    <?php else: ?>
        <p class="muted">No schedule generated yet. Use the form above to build the next draft.</p>
    <?php endif; ?>
</section>
<?php require __DIR__ . '/../partials/footer.php'; ?>
