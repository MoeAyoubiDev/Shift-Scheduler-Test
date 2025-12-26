<?php require __DIR__ . '/../partials/header.php'; ?>
<section class="setup-page">
    <?php $step = 'employees'; require __DIR__ . '/partials/stepper.php'; ?>

    <div class="card glass-card setup-card">
        <h1>Employees Setup</h1>
        <p class="muted">Add your team members or import from a file.</p>

        <div class="grid two-column">
            <div class="card glass-card light">
                <h3>Import from CSV</h3>
                <p class="muted">Upload employee data from a spreadsheet.</p>
                <button class="button secondary btn-secondary" type="button" disabled>Import CSV</button>
            </div>
            <div class="card glass-card light">
                <h3>Add Manually</h3>
                <p class="muted">Enter employee details one by one.</p>
                <form method="post" action="/setup/employees" class="form">
                    <div class="split">
                        <label>
                            Full Name
                            <input type="text" name="name" required>
                        </label>
                        <label>
                            Email Address
                            <input type="email" name="email" required>
                        </label>
                    </div>
                    <div class="split">
                        <label>
                            Role / Position
                            <select name="role" required>
                                <option value="employee">Employee</option>
                                <option value="senior">Senior</option>
                                <option value="supervisor">Supervisor</option>
                                <option value="team_leader">Team Leader</option>
                            </select>
                        </label>
                        <label>
                            Department
                            <select name="section_id" required>
                                <option value="">Select section</option>
                                <?php foreach ($sections as $section): ?>
                                    <option value="<?= (int) $section['id'] ?>"><?= htmlspecialchars($section['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                    </div>
                    <button type="submit" class="button btn-primary">Add Employee</button>
                </form>
            </div>
        </div>

        <div class="card glass-card light">
            <h3>Team Members</h3>
            <?php if ($employees): ?>
                <ul class="list">
                    <?php foreach ($employees as $employee): ?>
                        <li>
                            <div>
                                <strong><?= htmlspecialchars($employee['name']) ?></strong>
                                <span class="muted"><?= htmlspecialchars($employee['email']) ?></span>
                            </div>
                            <span class="pill"><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $employee['role']))) ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p class="muted">No employees added yet.</p>
            <?php endif; ?>
        </div>

        <div class="form-actions">
            <a class="button ghost" href="/setup/work-rules">Previous</a>
            <a class="button btn-primary" href="/setup/preferences">Next</a>
        </div>
    </div>
    <div class="setup-footer muted">3 of 5</div>
</section>
<?php require __DIR__ . '/../partials/footer.php'; ?>
