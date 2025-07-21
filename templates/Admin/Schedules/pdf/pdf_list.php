<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-size: 11px;
        color: #333;
        padding: 20px;
    }

    h2 {
        text-align: center;
        margin-bottom: 8px;
        font-size: 18px;
        color: #2c3e50;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .schedule-meta {
        text-align: center;
        font-size: 12px;
        color: #555;
        margin-bottom: 20px;
    }

    .calendar {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 25px;
        table-layout: fixed;
    }

    .calendar th,
    .calendar td {
        border: 1px solid #ccc;
        width: 14.28%;
        height: 100px;
        vertical-align: top;
        padding: 4px;
        text-align: left;
    }

    .calendar th {
        background-color: #2c3e50;
        color: white;
        font-weight: bold;
        font-size: 11px;
        text-align: center;
          padding: 4px;         /* reduce padding */
    height: 30px;         /* control height */
    }

    .date-number {
        font-size: 11px;
        font-weight: bold;
        color: #2c3e50;
        margin-bottom: 2px;
    }

    .shift-label {
        display: block;
        font-size: 10px;
        margin-top: 2px;
        padding: 2px 4px;
        border-radius: 4px;
        color: #000;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 100%;
        cursor: default;
    }

    .shift-morning {
        background-color: #2ecc71; /* Green */
    }

    .shift-evening {
        background-color: #f1c40f; /* Yellow */
    }

    .shift-other {
        background-color: #bdc3c7; /* Gray */
        color: #000;
    }

    .note {
        margin-top: 30px;
        font-size: 10px;
        text-align: center;
        color: #888;
    }
</style>

<h2>Employee Work Schedules</h2>

<?php if (!empty($selectedMonth) && !empty($selectedYear)): ?>
    <div class="schedule-meta">
        <?= h(date('F', mktime(0, 0, 0, (int)$selectedMonth, 1))) ?> <?= h($selectedYear) ?>
    </div>
<?php endif; ?>

<?php
use Cake\I18n\FrozenDate;

$daysOfWeek = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
$month = (int)$selectedMonth;
$year = (int)$selectedYear;
$firstDay = new FrozenDate("$year-$month-01");
$daysInMonth = (int)$firstDay->endOfMonth()->format('d');
$startWeekday = (int)$firstDay->format('w');
$currentDay = 1;
?>

<!-- Legend Section with Bottom Border -->
<div style="margin-top: 20px; margin-bottom: 30px; padding-bottom: 8px; display: inline-block;">
    <div class="legend" style="font-size: 10px;">
        <span class="shift-label shift-morning" style="margin-right: 8px;">Morning Shift</span>
        <span class="shift-label shift-evening"style="margin-right: 8px;">Evening Shift</span>
    </div>
</div>

<!-- Calendar Table -->
<table class="calendar">
    <thead>
        <tr>
            <?php foreach ($daysOfWeek as $day): ?>
                <th><?= $day ?></th>
            <?php endforeach; ?>
        </tr>
    </thead>
    <tbody>
        <?php while ($currentDay <= $daysInMonth): ?>
            <tr>
                <?php for ($i = 0; $i < 7; $i++): ?>
                    <?php
                        if ($currentDay === 1 && $i < $startWeekday) {
                            echo '<td></td>';
                            continue;
                        }

                        if ($currentDay > $daysInMonth) {
                            echo '<td></td>';
                            continue;
                        }

                        $dateObj = new FrozenDate("$year-$month-" . str_pad($currentDay, 2, '0', STR_PAD_LEFT));
                        $dateString = $dateObj->format('Y-m-d');
                        $cellContent = '<div class="date-number">' . $currentDay . '</div>';

                        $morningShifts = [];
                        $eveningShifts = [];
                        $otherShifts = [];

                        foreach ($groupedSchedules as $employeeName => $schedules) {
                            foreach ($schedules as $schedule) {
                                if ($schedule->work_date->format('Y-m-d') === $dateString) {
                                    $shiftName = strtolower($schedule->shift->shift_name ?? '');
                                    $labelText = h($employeeName) . ': ' . h($schedule->shift->shift_name ?? '-');
                                    $tooltip = h($labelText);

                                    if (str_contains($shiftName, 'morning')) {
                                        $morningShifts[] = '<span class="shift-label shift-morning" title="' . $tooltip . '">' . $labelText . '</span>';
                                    } elseif (str_contains($shiftName, 'evening')) {
                                        $eveningShifts[] = '<span class="shift-label shift-evening" title="' . $tooltip . '">' . $labelText . '</span>';
                                    } else {
                                        $otherShifts[] = '<span class="shift-label shift-other" title="' . $tooltip . '">' . $labelText . '</span>';
                                    }
                                }
                            }
                        }

                        $cellContent .= implode('', array_merge($morningShifts, $eveningShifts, $otherShifts));
                        echo "<td>$cellContent</td>";
                        $currentDay++;
                    ?>
                <?php endfor; ?>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<div class="note">
    This is a system-generated schedule. Please contact HR for any discrepancies.
</div>
