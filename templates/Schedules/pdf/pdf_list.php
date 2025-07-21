<style>
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    font-size: 12px;
    color: #333;
    padding: 20px;
}

h2 {
    text-align: center;
    margin-bottom: 5px;
    font-size: 20px;
    color: #2c3e50;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.schedule-meta {
    text-align: center;
    font-size: 13px;
    color: #7f8c8d;
    margin-bottom: 5px;
}

.employee-name {
    text-align: center;
    font-size: 14px;
    font-weight: bold;
    color: #34495e;
    margin-bottom: 15px;
    text-transform: capitalize;
}

.employee-name strong {
    color: #2c3e50;
}

.table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
}

.table th, .table td {
    border: 1px solid #bbb;
    padding: 10px 6px;
    text-align: center;
    vertical-align: middle;
}

.table th {
    background-color: #2c3e50;
    color: #ffffff;
    font-weight: bold;
}

.table td:first-child {
    color: #2c3e50; /* Dark blue for dates */
    font-weight: 500;
}

.table td:nth-child(2) {
    color: #16a085; /* Teal for shift */
    font-weight: 500;
}

.table tbody tr:nth-child(even) {
    background-color: #f4f6f7;
}

.note {
    margin-top: 30px;
    font-size: 11px;
    text-align: center;
    color: #888;
}
</style>

<h2>My Monthly Schedule</h2>
<div class="schedule-meta">
    <?= h($startDate->format('F Y')) ?>
</div>

<div class="employee-name">
    <strong><?= h($employee->full_name ?? 'Employee') ?></strong>
</div>

<table class="table">
    <thead>
        <tr>
            <th style="width: 60%;">Date</th>
            <th style="width: 40%;">Shift</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($schedules as $schedule): ?>
        <tr>
            <td><?= h($schedule->work_date->format('l, d M Y')) ?></td>
            <td><?= h($schedule->shift->shift_name ?? 'N/A') ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (count($schedules) === 0): ?>
        <tr>
            <td colspan="2">No schedules found.</td>
        </tr>
        <?php endif; ?>
    </tbody>
</table>

<div class="note">
    This is a system-generated schedule. Please contact HR for any discrepancies.
</div>
