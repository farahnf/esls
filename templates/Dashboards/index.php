<?php

echo $this->Html->script('qr-code-styling-1-5-0.min.js');
echo $this->Html->css('animate.min.css');
echo $this->Html->css('jquery.CalendarHeatmap');
echo $this->Html->script('moment.min.js');
echo $this->Html->script('jquery.CalendarHeatmap.min.js');
echo $this->Html->script('https://cdn.jsdelivr.net/npm/apexcharts');
echo $this->Html->script('https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js');
use Cake\Routing\Router;
?>

<!-- Include Bootstrap CSS and Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Morphext/2.4.4/morphext.css" integrity="sha256-iwSnUqgAndMlZnwFWAAzto9R/6Un2RBguZEITMb0Olk=" crossorigin="anonymous" />
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>


<!-- Dashboard Header -->
<div class="row mb-4">
    <div class="col-8">
        <h1 class="page_title"> <?= h($title) ?></h1>
        <h6 class="sub_title text-body-secondary"><?= h($system_name) ?></h6>
    </div>
</div>

<!-- Dashboard Stats -->
<?php if ($this->Identity->get('user_group_id') != 1): ?>
<div class="row mb-4">
    <div class="col-md-4 mb-3">
        <div class="card bg-primary text-white shadow-sm rounded-3">
            <div class="card-body text-center py-4">
                <h6 class="card-title">
                    <i class="bi bi-calendar-plus"></i> Annual Leave Remaining
                </h6>
                <p class="display-6 fw-bold"><?= $remainingAnnualLeave ?> days</p>
                <small class="text-light">Quota: 20 days</small>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card bg-secondary text-white shadow-sm rounded-3 h-100">
        <div class="card-body text-center py-4 d-flex flex-column justify-content-center">
            <h6 class="card-title">
                <i class="bi bi-calendar-check"></i> Used Annual Leave
            </h6>
            <p class="display-6 fw-bold mb-1"><?= $usedAnnualLeave ?> days</p>
            <small class="text-light">Total used so far</small>
        </div>
    </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card bg-danger text-white shadow-sm rounded-3">
            <div class="card-body text-center py-4">
                <h6 class="card-title">
                    <i class="bi bi-exclamation-triangle"></i> Unpaid Pregnancy Leave
                </h6>
                <p class="display-6 fw-bold"><?= $pregnancyUnpaidLeaves ?> days</p>
                <small class="text-light">Detected for ≥ 5 months pregnancy</small>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Leave List and Profile -->
<div class="row">
    <div class="col-md-9 mb-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title mb-3"><?= __('Leave List') ?></h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm table-hover">
                        <thead class="table-dark">
                            <tr>
                                <?php if ($this->Identity->get('user_group_id') == 1): ?>
                                    <th class="text-center"><?= __('Employee') ?></th>
                                <?php endif; ?>
                                <th class="text-center"><?= __('Leave Type') ?></th>
                                <th class="text-center"><?= __('Start Date') ?></th>
                                <th class="text-center"><?= __('End Date') ?></th>
                                <th class="text-center"><?= __('Status') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($leaves)): ?>
                                <?php foreach ($leaves as $leave): ?>
                                    <tr>
                                        <?php if ($this->Identity->get('user_group_id') == 1): ?>
                                            <td class="text-start">
                                                <?= $leave->has('employee') ? h($leave->employee->full_name ?? $leave->employee->name ?? 'N/A') : 'N/A' ?>
                                            </td>
                                        <?php endif; ?>
                                        <td class="text-start">
                                            <?= $leave->has('leave_type') ? h($leave->leave_type->leave_type_name) : '' ?>
                                        </td>
                                        <td class="text-center"><?= h($leave->start_date) ?></td>
                                        <td class="text-center"><?= h($leave->end_date) ?></td>
                                        <td class="text-center">
    <?php
        $statusClass = match ($leave->status) {
            'Approved' => 'bg-success text-white px-2 py-1 rounded',
            'Rejected' => 'bg-danger text-white px-2 py-1 rounded',
            'Pending'  => 'bg-warning text-dark px-2 py-1 rounded',
            default    => ''
        };
    ?>
    <span class="<?= $statusClass ?>">
        <?= h($leave->status) ?>
    </span>
</td>

                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="<?= $this->Identity->get('user_group_id') == 1 ? 6 : 5 ?>" class="text-center text-muted">No leave applications found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Profile -->
    <div class="col-md-3 mb-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title mb-2">Profile</h5>
                <div class="tricolor_line mb-3"></div>
                <div class="text-center mb-3">
                    <?php if ($this->Identity->get('avatar')): ?>
                        <?= $this->Html->image('../files/Users/avatar/' . $this->Identity->get('slug') . '/' . $this->Identity->get('avatar'), ['class' => 'rounded', 'width' => '100', 'height' => '100']) ?>
                    <?php else: ?>
                        <?= $this->Html->image('avatar_default.png', ['alt' => 'avatar', 'class' => 'rounded', 'width' => '100', 'height' => '100']) ?>
                    <?php endif; ?>
                </div>
                <p class="text-center mb-1 fw-bold"><?= h($this->Identity->get('fullname')) ?></p>
                <p class="text-center small text-muted">
                    <?php
                    switch ($this->Identity->get('user_group_id')) {
                        case 1: echo 'Administrator'; break;
                        case 2: echo 'Moderator'; break;
                        case 3: echo 'User'; break;
                        default: echo 'Unknown';
                    }
                    ?>
                </p>
                <div class="text-center">
                    <a class="btn btn-outline-warning btn-sm" data-bs-toggle="collapse" href="#collapseActivity" role="button" aria-expanded="false" aria-controls="collapseActivity">
                        Account Activity
                    </a>
                </div>
                <div class="collapse mt-3" id="collapseActivity">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Action</th>
                                    <th>Date/Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($userLogs as $userLog): ?>
                                    <tr>
                                        <td>
                                            <?php
                                            echo match ($userLog->action) {
                                                'Login' => '<span class="badge bg-success">Login</span>',
                                                'Logout' => '<span class="badge bg-danger">Logout</span>',
                                                default => '<span class="badge bg-secondary">Other</span>',
                                            };
                                            ?>
                                        </td>
                                        <td><?= date('M d, Y (h:i A)', strtotime($userLog->created)) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
