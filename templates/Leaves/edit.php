<?php 
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Leave $leave
 * @var string[]|\Cake\Collection\CollectionInterface $employees
 * @var string[]|\Cake\Collection\CollectionInterface $leaveTypes
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
            <button class="btn p-0 border-0" type="button" id="orderStatistics" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa-solid fa-bars text-primary"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="orderStatistics">
                <?= $this->Form->postLink(
                    __('Delete'),
                    ['action' => 'delete', $leave->leave_id],
                    ['confirm' => __('Are you sure you want to delete # {0}?', $leave->leave_id), 'class' => 'dropdown-item', 'escapeTitle' => false]
                ) ?>
                <?= $this->Html->link(__('List Leaves'), ['action' => 'index'], ['class' => 'dropdown-item', 'escapeTitle' => false]) ?>
            </div>
        </div>
    </div>
</div>
<div class="line mb-4"></div>
<!-- /Header -->

<!-- Form Card -->
<div class="card rounded-0 mb-3 bg-dark-turquoise border-0 shadow-sm">
    <div class="card-body">
        <?= $this->Form->create($leave) ?>
        <fieldset>
            <legend><?= __('Edit Leave') ?></legend>
            
            <div class="mb-3">
                <?php echo $this->Form->control('employee_id', ['options' => $employees, 'empty' => true, 'class' => 'form-select']); ?>
            </div>
            <div class="mb-3">
                <?php echo $this->Form->control('leave_type_id', ['options' => $leaveTypes, 'empty' => true, 'class' => 'form-select']); ?>
            </div>
            <div class="mb-3">
                <?php echo $this->Form->control('start_date', ['empty' => true, 'class' => 'form-control']); ?>
            </div>
            <div class="mb-3">
                <?php echo $this->Form->control('end_date', ['empty' => true, 'class' => 'form-control']); ?>
            </div>
            <div class="mb-3">
                <?php echo $this->Form->control('reason', ['class' => 'form-control']); ?>
            </div>
            <div class="mb-3">
                <?php echo $this->Form->control('status', ['class' => 'form-select']); ?>
            </div>
            <div class="mb-3">
                <?php echo $this->Form->control('applied_on', ['class' => 'form-control']); ?>
            </div>
        </fieldset>

        <div class="text-end">
            <?= $this->Form->button('Reset', ['type' => 'reset', 'class' => 'btn btn-outline-warning me-2']); ?>
            <?= $this->Form->button(__('Submit'), ['type' => 'submit', 'class' => 'btn btn-dark-turquoise']) ?>
        </div>

        <?= $this->Form->end() ?>
    </div>
</div>
<!-- /Form Card -->
