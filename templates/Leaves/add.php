<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Leave $leave
 * @var \Cake\Collection\CollectionInterface|string[] $employees
 * @var \Cake\Collection\CollectionInterface|string[] $leaveTypes
 * @var array $leaveByCategory
 * @var array $leaveCategoryStats
 * @var int $remainingAnnualLeave
 */
?>
<!-- Header -->
<div class="row text-body-secondary mb-3">
    <div class="col-md-10">
        <h1 class="my-0 page_title"><?= h($title) ?></h1>
        <h6 class="sub_title text-muted"><?= h($system_name) ?></h6>
    </div>
    <div class="col-md-2 text-end">
        <div class="dropdown">
            <button class="btn btn-sm p-0 border-0" type="button" id="menuDropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa-solid fa-bars text-primary fs-4"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="menuDropdown">
                <?= $this->Html->link(__('List Leaves'), ['action' => 'index'], ['class' => 'dropdown-item']) ?>
            </div>
        </div>
    </div>
</div>
<div class="line mb-4"></div>

<!-- ✅ Leave Usage Overview -->
<?php if (!empty($leaveByCategory)): ?>
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body">
            <h5 class="mb-3 text-primary">Your Leave Usage by Category</h5>
            <ul class="list-group list-group-flush">
                <?php foreach ($leaveByCategory as $typeName => $usedDays): ?>
                    <?php
                        $badgeClass = 'bg-secondary';
                        if (stripos($typeName, 'annual') !== false) $badgeClass = 'bg-success';
                        elseif (stripos($typeName, 'emergency') !== false) $badgeClass = 'bg-danger';
                        elseif (stripos($typeName, 'pregnancy') !== false) $badgeClass = 'bg-warning text-dark';
                        elseif (stripos($typeName, 'replacement') !== false) $badgeClass = 'bg-info text-dark';
                    ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <?= h($typeName) ?>
                        <span class="badge <?= $badgeClass ?> rounded-pill">
                            <?= $usedDays ?> day(s)
                            <?php if (stripos($typeName, 'annual') !== false && isset($remainingAnnualLeave)): ?>
                                <small class="ms-2">(Remaining: <?= $remainingAnnualLeave ?>)</small>
                            <?php endif; ?>
                        </span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
<?php endif; ?>

<!-- ✅ Leave Category Descriptions Accordion -->
<div class="accordion mb-4" id="leaveCategoryDescriptions">
    <div class="accordion-item">
        <h2 class="accordion-header" id="headingOne">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne">
                View Leave Category Descriptions
            </button>
        </h2>
        <div id="collapseOne" class="accordion-collapse collapse" data-bs-parent="#leaveCategoryDescriptions">
            <div class="accordion-body">
                <ul class="mb-0">
                    <li><strong>Annual Leave:</strong> Standard paid leave. Max 20 days/year.</li>
                    <li><strong>Others:</strong> Miscellaneous leave. Does not deduct annual.</li>
                    <li><strong>Pregnancy Leave:</strong> Unpaid from 5 months onward.</li>
                    <li><strong>Emergency Leave:</strong> Urgent leave, deducts annual quota.</li>
                    <li><strong>Replacement Leave:</strong> For working on rest days. No deduction.</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- ✅ Grouped leave types (example IDs) -->
<?php
$grouped = [
    'Annual Leave'    => [3 => 'Annual Leave'],
    'Emergency Leave' => [2 => 'Emergency Leave'],
    'Others'          => [
        'pregnancy'   => 'Pregnancy Leave',
        'replacement' => 'Replacement Leave',
        1             => 'Sick Leave',
    ],
];
?>

<!-- ✅ Leave Form -->
<div>
    <div class="card-body p-4">
        <?= $this->Form->create($leave, ['type' => 'file']) ?>
        <fieldset>
            <legend class="mb-4"><?= __('Add Leave') ?></legend>

            <div class="mb-3">
                <?= $this->Form->control('leave_type_id', [
                    'type'    => 'select',
                    'options' => $grouped,
                    'empty'   => '— Select type —',
                    'class'   => 'form-select',
                    'label'   => 'Leave Type'
                ]) ?>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <?= $this->Form->control('start_date', [
                        'empty' => true,
                        'class' => 'form-control',
                        'label' => 'Start Date',
                        'id' => 'start-date'
                    ]) ?>
                </div>
                <div class="col-md-6 mb-3">
                    <?= $this->Form->control('end_date', [
                        'empty' => true,
                        'class' => 'form-control',
                        'label' => 'End Date',
                        'id' => 'end-date'
                    ]) ?>
                </div>
            </div>

            <div class="mb-3">
                <?= $this->Form->control('reason', [
                    'class' => 'form-control',
                    'label' => 'Reason'
                ]) ?>
            </div>

            <div class="mb-3">
                <?= $this->Form->control('applied_on', [
                    'class' => 'form-control',
                    'label' => 'Applied On',
                    'value' => date('Y-m-d H:i:s')
                ]) ?>
            </div>

            <div class="mb-4">
                <?= $this->Form->control('attachment', [
                    'type' => 'file',
                    'label' => 'Attach File (optional)',
                    'class' => 'form-control'
                ]) ?>
            </div>
        </fieldset>

        <div class="text-end">
            <?= $this->Form->button('Reset', ['type' => 'reset', 'class' => 'btn btn-outline-secondary me-2']) ?>
            <?= $this->Form->button(__('Submit'), ['class' => 'btn btn-primary']) ?>
        </div>

        <?= $this->Form->end() ?>
    </div>

    <!-- ✅ JS to Calculate Total Days -->
    <?= $this->Html->scriptStart(['block' => true]) ?>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const startInput = document.getElementById('start-date');
        const endInput = document.getElementById('end-date');

        const today = new Date().toISOString().split('T')[0];
        startInput.setAttribute('min', today);
        endInput.setAttribute('min', today);

        const totalDaysDisplay = document.createElement('div');
        totalDaysDisplay.classList.add('mt-2', 'fw-bold', 'text-primary');
        endInput.parentNode.appendChild(totalDaysDisplay);

        function calculateDays() {
            const start = new Date(startInput.value);
            const end = new Date(endInput.value);

            if (!isNaN(start) && !isNaN(end)) {
                const timeDiff = end.getTime() - start.getTime();
                const diffDays = Math.floor(timeDiff / (1000 * 3600 * 24)) + 1;

                if (diffDays > 0) {
                    totalDaysDisplay.textContent = `Total Days: ${diffDays}`;
                } else {
                    totalDaysDisplay.textContent = "End date must be after start date.";
                }
            } else {
                totalDaysDisplay.textContent = '';
            }
        }

        startInput?.addEventListener('change', calculateDays);
        endInput?.addEventListener('change', calculateDays);
    });
    </script>
    <?= $this->Html->scriptEnd() ?>
</div>