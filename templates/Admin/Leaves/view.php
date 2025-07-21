<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Leave $leave
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
							<li><?= $this->Html->link(__('Edit Leave'), ['action' => 'edit', $leave->leave_id], ['class' => 'dropdown-item', 'escapeTitle' => false]) ?></li>
				<li><?= $this->Form->postLink(__('Delete Leave'), ['action' => 'delete', $leave->leave_id], ['confirm' => __('Are you sure you want to delete # {0}?', $leave->leave_id), 'class' => 'dropdown-item', 'escapeTitle' => false]) ?></li>
				<li><hr class="dropdown-divider"></li>
				<li><?= $this->Html->link(__('List Leaves'), ['action' => 'index'], ['class' => 'dropdown-item', 'escapeTitle' => false]) ?></li>
				<li><?= $this->Html->link(__('New Leave'), ['action' => 'add'], ['class' => 'dropdown-item', 'escapeTitle' => false]) ?></li>
							</div>
		</div>
    </div>
</div>
<div class="line mb-4"></div>
<!--/Header-->

<div class="row">
	<div class="col-md-9">
		<div class="card rounded-0 mb-3 bg-body-tertiary border-0 shadow">
			<div class="card-body text-body-secondary">
            <h3><?= h($leave->leave_id) ?></h3>
    <div class="table-responsive">
        <table class="table">
                <tr>
                    <th><?= __('Employee') ?></th>
                    <td><?= $leave->hasValue('employee') ? $this->Html->link($leave->employee->email, ['controller' => 'Employees', 'action' => 'view', $leave->employee->employee_id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Leave Type') ?></th>
                    <td><?= $leave->hasValue('leave_type') ? $this->Html->link($leave->leave_type->leave_type_name, ['controller' => 'Leavetypes', 'action' => 'view', $leave->leave_type->leave_type_id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Status') ?></th>
                    <td><?= h($leave->status) ?></td>
                </tr>
                <tr>
                    <th><?= __('Leave Id') ?></th>
                    <td><?= $this->Number->format($leave->leave_id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Start Date') ?></th>
                    <td><?= h($leave->start_date) ?></td>
                </tr>
                <tr>
                    <th><?= __('End Date') ?></th>
                    <td><?= h($leave->end_date) ?></td>
                </tr>
                <tr>
                    <th><?= __('Applied On') ?></th>
                    <td><?= h($leave->applied_on) ?></td>
                </tr>
            </table>
            </div>
            <div class="text">
                <strong><?= __('Reason') ?></strong>
                <blockquote>
                    <?= $this->Text->autoParagraph(h($leave->reason)); ?>
                </blockquote>
            </div>

			</div>
		</div>
		

            
            


		
	</div>
	<div class="col-md-3">
	  Column
	</div>
</div>




