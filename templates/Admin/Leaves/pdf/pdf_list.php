<?php
use Cake\I18n\FrozenDate;

// Group leaves by type
$summary = [];
foreach ($leaves as $leave) {
    $type = $leave->leave_type->leave_type_name ?? 'Unknown';
    $summary[$type] = ($summary[$type] ?? 0) + 1;
}

// Status color function
function getStatusColor($status) {
    switch (strtolower($status)) {
        case 'approved': return '#16a34a'; // Green
        case 'pending':  return '#f59e0b'; // Orange
        case 'rejected': return '#dc2626'; // Red
        default:         return '#6b7280'; // Gray
    }
}
?>

<h2 style="text-align:center; margin-bottom: 5px;">Leave Report</h2>
<p style="text-align:center; margin-bottom: 20px;">
    <?= date('F', mktime(0, 0, 0, $month, 10)) ?> <?= h($year) ?>
</p>

<!-- Main Table -->
<table border="1" cellspacing="0" cellpadding="6" width="100%" style="font-size: 12px; border-collapse: collapse;">
    <thead>
        <tr style="background-color: #f3f4f6;">
            <th style="width: 18%;">Employee Name</th>
            <th style="width: 18%;">Leave Type</th>
            <th style="width: 14%;">Start Date</th>
            <th style="width: 14%;">End Date</th>
            <th style="width: 14%;">Status</th>
            <th style="width: 22%;">Applied On</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($leaves as $leave): ?>
        <tr>
            <td><?= h($leave->employee->full_name ?? '-') ?></td>
            <td><?= h($leave->leave_type->leave_type_name ?? '-') ?></td>
            <td><?= h($leave->start_date) ?></td>
            <td><?= h($leave->end_date) ?></td>
            <td style="color: <?= getStatusColor($leave->status) ?>; font-weight: bold;">
                <?= h(ucfirst($leave->status)) ?>
            </td>
            <td><?= h($leave->applied_on) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<!-- Summary Section -->
<h3 style="margin-top: 30px; font-size: 14px;">Leave Summary by Type</h3>
<table border="1" cellspacing="0" cellpadding="6" style="font-size: 12px; width: 300px; border-collapse: collapse;">
    <thead>
        <tr style="background-color: #e2e8f0;">
            <th style="width: 70%;">Leave Type</th>
            <th style="width: 30%; text-align:center;">Total</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($summary as $type => $count): ?>
        <tr>
            <td><?= h($type) ?></td>
            <td style="text-align: center;"><?= $count ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<!-- Printed Date -->
<p style="margin-top: 40px; font-size: 11px; text-align: right;">
    Printed on: <?= date('d M Y, h:i A') ?>
</p>
