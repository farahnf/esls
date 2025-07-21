<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Leave $leave
 * @var \Cake\Collection\CollectionInterface|string[] $employees
 * @var \Cake\Collection\CollectionInterface|string[] $leaveTypes
 */
?>
<!-- Header -->
<div class="row text-body-secondary">
    <div class="col-10">
        <h1 class="my-0 page_title"><?= h($title) ?></h1>
        <h6 class="sub_title text-body-secondary"><?= h($system_name) ?></h6>
    </div>
    <div class="col-2 text-end">
        <div class="dropdown mx-3 mt-2">
            <button class="btn p-0 border-0" type="button" id="orederStatistics" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa-solid fa-bars text-primary"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="orederStatistics">
                <?= $this->Html->link(__('List Leaves'), ['action' => 'index'], ['class' => 'dropdown-item', 'escapeTitle' => false]) ?>
            </div>
        </div>
    </div>
</div>
<div class="line mb-4"></div>
<!-- /Header -->

<div class="card rounded-0 mb-3 bg-body-tertiary border-0 shadow">
    <div class="card-body text-body-secondary">
        <?= $this->Form->create($leave, ['type' => 'file']) ?>
        <fieldset>
            <legend class="mb-4"><?= __('Add Leave') ?></legend>

            <div class="mb-3">
                <?= $this->Form->control('employee_id', ['options' => $employees, 'label' => 'Employee', 'empty' => 'Select employee']) ?>
            </div>

            <div class="mb-3">
                <?= $this->Form->control('leave_type_id', ['options' => $leaveTypes, 'label' => 'Leave Type', 'empty' => 'Select leave type']) ?>
            </div>

            <div class="mb-3">
                <?= $this->Form->control('start_date', ['label' => 'Start Date']) ?>
            </div>

            <div class="mb-3">
                <?= $this->Form->control('end_date', ['label' => 'End Date']) ?>
            </div>

            <div class="mb-3">
                <?= $this->Form->control('reason', ['label' => 'Reason']) ?>
            </div>

            <div class="mb-3">
                <?= $this->Form->control('status', ['label' => 'Status']) ?>
            </div>

            <div class="mb-3">
                <?= $this->Form->control('applied_on', ['label' => 'Applied On']) ?>
            </div>

               <!-- ✅ Add file upload input here -->
            <div class="mb-3">
                <?= $this->Form->control('attachment', [
                    'type' => 'file',
                    'label' => 'Attach File (optional)',
                    'required' => false
                ]) ?>
            </div>

        </fieldset>

        <div class="text-end">
            <?= $this->Form->button('Reset', ['type' => 'reset', 'class' => 'btn btn-outline-warning']) ?>
            <?= $this->Form->button(__('Submit'), ['type' => 'submit', 'class' => 'btn btn-outline-primary']) ?>
        </div>
        <?= $this->Form->end() ?>
    </div>
</div>