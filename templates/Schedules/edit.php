<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Schedule $schedule
 * @var string[]|\Cake\Collection\CollectionInterface $employees
 * @var string[]|\Cake\Collection\CollectionInterface $shifts
 */
?>
<!--Header-->
<div class="row text-body-secondary">
	<div class="col-10">
		<h1 class="my-0 page_title"><?php echo $title; ?></h1>
		<h6 class="sub_title text-body-secondary"><?php echo $system_name; ?></h6>
	</div>
	<div class="col-2 text-end">
		<div class="dropdown mx-3 mt-2">
			<button class="btn p-0 border-0" type="button" id="orederStatistics" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			<i class="fa-solid fa-bars text-primary"></i>
			</button>
				<div class="dropdown-menu dropdown-menu-end" aria-labelledby="orederStatistics">
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $schedule->schedule_id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $schedule->schedule_id), 'class' => 'dropdown-item', 'escapeTitle' => false]
            ) ?>
            <?= $this->Html->link(__('List Schedules'), ['action' => 'index'], ['class' => 'dropdown-item', 'escapeTitle' => false]) ?>
				</div>
		</div>
    </div>
</div>
<div class="line mb-4"></div>
<!--/Header-->

<div class="card rounded-0 mb-3 bg-body-tertiary border-0 shadow">
    <div class="card-body text-body-secondary">
            <?= $this->Form->create($schedule) ?>
            <fieldset>
                <legend><?= __('Edit Schedule') ?></legend>
                
                    <?php echo $this->Form->control('employee_id', ['options' => $employees, 'empty' => true]); ?>
                    <?php echo $this->Form->control('shift_id', ['options' => $shifts, 'empty' => true]); ?>
                    <?php echo $this->Form->control('work_date', ['empty' => true]); ?>
                    <?php echo $this->Form->control('status'); ?>
               
            </fieldset>
				<div class="text-end">
				  <?= $this->Form->button('Reset', ['type' => 'reset', 'class' => 'btn btn-outline-warning']); ?>
				  <?= $this->Form->button(__('Submit'),['type' => 'submit', 'class' => 'btn btn-outline-primary']) ?>
                </div>
        <?= $this->Form->end() ?>
    </div>
</div>