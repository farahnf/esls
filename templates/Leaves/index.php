<?php 
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Leave> $leaves
 */
use Cake\Routing\Router;

echo $this->Html->css('select2/css/select2.css');
echo $this->Html->script('select2/js/select2.full.min.js');
echo $this->Html->css('jquery.datetimepicker.min.css');
echo $this->Html->script('jquery.datetimepicker.full.js');
echo $this->Html->script('https://cdn.jsdelivr.net/npm/apexcharts');
echo $this->Html->script('https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js');

$c_name = $this->request->getParam('controller');
echo $this->Html->script('bootstrapModal', ['block' => 'scriptBottom']);
?>

<!-- Header -->
<div class="row text-body-secondary mb-4">
    <div class="col-10">
        <h1 class="page_title"><?= h($title) ?></h1>
        <h6 class="sub_title text-body-secondary"><?= h($system_name) ?></h6>
    </div>
    <div class="col-2 text-end">
        <!-- Apply Leave Button -->
        <?= $this->Html->link(__('Apply Leave'), ['action' => 'add'], ['class' => 'btn btn-outline-primary btn-sm mt-2']) ?>
    </div>
</div>

<!-- Leave List Section -->
<div class="card bg-body-tertiary border-0 shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-sm text-center align-middle">
                <thead>
                    <tr>
                        <th><?= $this->Paginator->sort('employee.full_name', 'Name') ?></th>
                        <th><?= $this->Paginator->sort('leave_type.leave_type_name', 'Leave Type') ?></th>
                        <th><?= $this->Paginator->sort('start_date', 'Start Date') ?></th>
                        <th><?= $this->Paginator->sort('end_date', 'End Date') ?></th>
                        <th><?= $this->Paginator->sort('status') ?></th>
                        <th><?= $this->Paginator->sort('applied_on', 'Applied On') ?></th>
                        <th><?= $this->Paginator->sort('actions', 'Actions') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $page = $this->Paginator->counter('{{page}}');
                        $limit = 10;
                        $counter = ($page * $limit) - $limit + 1;
                    ?>
                    <?php foreach ($leaves as $leave): ?>
                        <tr>
                           <td class="text-start"><?= h($leave->employee->full_name ?? '-') ?></td>
                            <td><?= h($leave->leave_type->leave_type_name ?? '-') ?></td>
                            <td><?= h(date('d M Y', strtotime($leave->start_date))) ?></td>
                            <td><?= h(date('d M Y', strtotime($leave->end_date))) ?></td>
                            <td>
                                <?php
                                    $status = strtolower($leave->status);
                                    switch ($status) {
                                        case 'approved':
                                            echo '<span class="badge bg-success">Approved</span>';
                                            break;
                                        case 'pending':
                                            echo '<span class="badge bg-warning text-dark">Pending</span>';
                                            break;
                                        case 'rejected':
                                            echo '<span class="badge bg-danger">Rejected</span>';
                                            break;
                                        default:
                                            echo h($leave->status);
                                    }
                                ?>
                            </td>
                            <td><?= h(date('d M Y', strtotime($leave->applied_on))) ?></td>
                            <td class="actions">
                                <div class="btn-group" role="group">
                                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $leave->leave_id], [
                                        'confirm' => __('Are you sure you want to delete Leave # {0}?', $leave->leave_id),
                                        'class' => 'btn btn-outline-danger btn-sm'
                                    ]) ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


        <!-- Pagination -->
        <div class="mt-3">
            <ul class="pagination justify-content-end">
                <?= $this->Paginator->first('<< ' . __('First')) ?>
                <?= $this->Paginator->prev('< ' . __('Previous')) ?>
                <?= $this->Paginator->numbers(['before' => '', 'after' => '']) ?>
                <?= $this->Paginator->next(__('Next') . ' >') ?>
                <?= $this->Paginator->last(__('Last') . ' >>') ?>
            </ul>
            <div class="text-end"><?= $this->Paginator->counter(__('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')) ?></div>
        </div>
    </div>
</div>
            <!-- Report Tab -->
            <div class="tab-pane container fade px-0" id="report">
                <!-- Statistics Cards -->
                <div class="row pb-3">
                    <div class="col-md-4">
                        <div class="stat_card card-1 bg-body-tertiary">
                            <h3><?= $total_leaves ?></h3>
                            <p>Total Leaves</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat_card card-2 bg-body-tertiary">
                            <h3><?= $total_leaves_active ?></h3>
                            <p>Active Leaves</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat_card card-3 bg-body-tertiary">
                            <h3><?= $total_leaves_archived ?></h3>
                            <p>Archived Leaves</p>
                        </div>
                    </div>
                    <?php if (isset($usedAnnualLeave)): ?>
    <div class="col-md-4">
        <div class="stat_card card-4 bg-body-tertiary">
            <h3><?= $usedAnnualLeave ?> days</h3>
            <p>Used Annual Leave</p>
        </div>
    </div>
<?php endif; ?>

<?php if (isset($remainingAnnualLeave)): ?>
    <div class="col-md-4">
        <div class="stat_card card-5 bg-body-tertiary">
            <h3><?= $remainingAnnualLeave ?> days</h3>
            <p>Annual Leave Remaining</p>
        </div>
    </div>
<?php endif; ?>

<?php if (!empty($pregnancyUnpaidLeaves)): ?>
    <div class="col-md-4">
        <div class="stat_card card-6 bg-body-tertiary">
            <h3><?= $pregnancyUnpaidLeaves ?> days</h3>
            <p>Pregnancy (Unpaid)</p>
        </div>
    </div>
<?php endif; ?>
                <!-- Monthly Leaves Chart -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="card bg-body-tertiary border-0 shadow mb-4">
                            <div class="card-body">
                                <div class="card-title mb-0">Leaves (Monthly)</div>
                                <div class="tricolor_line mb-3"></div>
                                <div class="chart-container" style="position: relative;">
                                    <canvas id="monthly"></canvas>
                                </div>
                                <script>
                                    const ctx = document.getElementById('monthly');
                                    new Chart(ctx, {
                                        type: 'bar',
                                        data: {
                                            labels: <?= json_encode($monthArray); ?>,
                                            datasets: [{
                                                label: '# of Leaves(s)',
                                                data: <?= json_encode($countArray); ?>,
                                                backgroundColor: [
                                                    'rgba(255, 99, 132, 0.2)', 'rgba(54, 162, 235, 0.2)', 'rgba(255, 206, 86, 0.2)',
                                                    'rgba(75, 192, 192, 0.2)', 'rgba(153, 102, 255, 0.2)', 'rgba(89, 233, 28, 0.2)',
                                                    'rgba(255, 5, 5, 0.2)', 'rgba(255, 128, 0, 0.2)', 'rgba(153, 153, 153, 0.2)',
                                                    'rgba(15, 207, 210, 0.2)', 'rgba(44, 13, 181, 0.2)', 'rgba(86, 172, 12, 0.2)'
                                                ],
                                                borderColor: [
                                                    'rgba(255, 99, 132, 1)', 'rgba(54, 162, 235, 1)', 'rgba(255, 206, 86, 1)',
                                                    'rgba(75, 192, 192, 1)', 'rgba(153, 102, 255, 1)', 'rgba(89, 233, 28, 1)',
                                                    'rgba(255, 5, 5, 1)', 'rgba(255, 128, 0, 1)', 'rgba(153, 153, 153, 1)',
                                                    'rgba(15, 207, 210, 1)', 'rgba(44, 13, 181, 1)', 'rgba(86, 172, 12, 1)'
                                                ],
                                                borderWidth: 1
                                            }]
                                        },
                                        options: {
                                            scales: {
                                                y: {
                                                    beginAtZero: true
                                                }
                                            },
                                            plugins: {
                                                title: {
                                                    display: false,
                                                    text: 'Leaves (Monthly)'
                                                },
                                                subtitle: {
                                                    display: false,
                                                    text: '<?= $system_name ?>'
                                                },
                                                legend: {
                                                    display: false
                                                }
                                            }
                                        }
                                    });
                                </script>
                            </div>
                        </div>
                    </div>

                    <!-- Leaves by Status Chart -->
                    <div class="col-md-6">
                        <div class="card bg-body-tertiary border-0 shadow mb-4">
                            <div class="card-body">
                                <div class="card-title mb-0">Leaves by Status</div>
                                <div class="tricolor_line mb-3"></div>
                                <div class="chart-container" style="position: relative;">
                                    <canvas id="status"></canvas>
                                </div>
                                <script>
                                    const ctx_2 = document.getElementById('status');
                                    new Chart(ctx_2, {
                                        type: 'bar',
                                        data: {
                                            labels: ['Active', 'Disabled', 'Archived'],
                                            datasets: [{
                                                label: '# of Leaves(s)',
                                                data: [<?= json_encode($total_leaves_active); ?>, <?= json_encode($total_leaves_disabled); ?>, <?= json_encode($total_leaves_archived); ?>],
                                                backgroundColor: ['rgba(255, 99, 132, 0.2)', 'rgba(54, 162, 235, 0.2)', 'rgba(255, 206, 86, 0.2)'],
                                                borderColor: ['rgba(255, 99, 132, 1)', 'rgba(54, 162, 235, 1)', 'rgba(255, 206, 86, 1)'],
                                                borderWidth: 1
                                            }]
                                        },
                                        options: {
                                            scales: {
                                                y: {
                                                    beginAtZero: true
                                                }
                                            },
                                            plugins: {
                                                title: {
                                                    display: false,
                                                    text: 'Leaves by Status'
                                                },
                                                subtitle: {
                                                    display: false,
                                                    text: '<?= $system_name ?>'
                                                },
                                                legend: {
                                                    display: false
                                                }
                                            }
                                        }
                                    });
                                </script>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Export Tab -->
            <div class="tab-pane container fade px-0" id="export">
                <?php
                    $domain = Router::url("/", true);
                    $sub = 'leaves';
                    $combine = $domain . $sub;
                ?>
                <div class="row pb-3">
                    <div class="col-md-3 mb-2">
                        <a href='<?php echo $combine; ?>/csv' class="kosong">
                            <div class="card bg-body-tertiary border-0 shadow">
                                <div class="card-body">
                                    <div class="row mx-0">
                                        <div class="col-5 text-center mt-3 mb-3"><i class="fa-solid fa-file-csv fa-2x text-primary"></i></div>
                                        <div class="col-7 text-end m-auto">
                                            <div class="fs-4 fw-bold">CSV</div>
                                            <div class="small-text"><i class="fa-solid fa-angles-down fa-flip"></i> Download</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href='<?php echo $combine; ?>/json' class="kosong" target="_blank">
                            <div class="card bg-body-tertiary border-0 shadow">
                                <div class="card-body">
                                    <div class="row mx-0">
                                        <div class="col-5 text-center mt-3 mb-3"><i class="fa-solid fa-braille fa-2x text-warning"></i></div>
                                        <div class="col-7 text-end m-auto">
                                            <div class="fs-4 fw-bold">JSON</div>
                                            <div class="small-text"><i class="fa-solid fa-angles-down fa-flip"></i> Download</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href='<?php echo $combine; ?>/pdfList' class="kosong">
                            <div class="card bg-body-tertiary border-0 shadow">
                                <div class="card-body">
                                    <div class="row mx-0">
                                        <div class="col-5 text-center mt-3 mb-3"><i class="fa-regular fa-file-pdf fa-2x text-danger"></i></div>
                                        <div class="col-7 text-end m-auto">
                                            <div class="fs-4 fw-bold">PDF</div>
                                            <div class="small-text"><i class="fa-solid fa-angles-down fa-flip"></i> Download</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>  
            </div>
        </div>
    </div>
</div>
