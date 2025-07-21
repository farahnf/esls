<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Employee> $employees
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
<!--Header-->
<div class="row text-body-secondary">
	<div class="col-10">
		<h1 class="my-0 page_title"><?= h($title); ?></h1>
		<h6 class="sub_title text-body-secondary"><?= h($system_name); ?></h6>
	</div>
	<div class="col-2 text-end">
		<?= $this->Html->link(
			__('<i class="fa-solid fa-plus"></i> New Employee'),
			['controller' => 'Employees', 'action' => 'add', 'prefix' => 'Admin'],
			['class' => 'btn btn-outline-primary btn-sm mt-2', 'escapeTitle' => false]
		) ?>
	</div>
</div>
<div class="line mb-4"></div>
<div class="tab-content px-0">
	<div class="tab-pane fade active show" id="list">
		<div class="card bg-body-tertiary border-0 shadow mb-4">
			<div class="card-body text-body-secondary">
				<div class="table-responsive">
					<table class="table table-sm table-border mb-4 table_transparent table-hover">
						<thead>
							<?php
								$page = $this->Paginator->counter('{{page}}');
								$limit = 10; 
								$counter = ($page * $limit) - $limit + 1;
							?>
							<tr>
								<th><?= __('No.') ?></th>
								<th><?= $this->Paginator->sort('full_name') ?></th>
								<th><?= $this->Paginator->sort('email') ?></th>
								<th><?= $this->Paginator->sort('phone') ?></th>
								<th><?= $this->Paginator->sort('hire_date') ?></th>
								<th><?= $this->Paginator->sort('rest_day', 'Rest Day') ?></th>
								<th class="actions"><?= __('Actions') ?></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($employees as $employee): ?>
							<tr>
								<td><?= $this->Number->format($employee->employee_id) ?></td>
								<td><?= h($employee->full_name) ?></td>
								<td><?= h($employee->email) ?></td>
								<td><?= h($employee->phone) ?></td>
								<td><?= h($employee->hire_date) ?></td>
								<td><?= h($employee->rest_day) ?></td>
								<td class="actions text-center">
									<div class="btn-group shadow" role="group" aria-label="Basic example">
										<?= $this->Html->link(__('<i class="far fa-folder-open"></i>'), ['action' => 'view', $employee->employee_id], ['class' => 'btn btn-outline-primary btn-xs', 'escapeTitle' => false]) ?>
										<?= $this->Html->link(__('<i class="fa-regular fa-pen-to-square"></i>'), ['action' => 'edit', $employee->employee_id], ['class' => 'btn btn-outline-warning btn-xs', 'escapeTitle' => false]) ?>
										<?php $this->Form->setTemplates(['confirmJs' => 'addToModal("{{formName}}"); return false;']); ?>
										<?= $this->Form->postLink(
											__('<i class="fa-regular fa-trash-can"></i>'),
											['action' => 'delete', $employee->employee_id],
											[
												'confirm' => __('Are you sure you want to delete Employees: "{0}"?', $employee->employee_id),
												'title' => __('Delete'),
												'class' => 'btn btn-outline-danger btn-xs',
												'escapeTitle' => false,
												'data-bs-toggle' => "modal",
												'data-bs-target' => "#bootstrapModal"
											]
										) ?>
									</div>
								</td>
							</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
				<div aria-label="Page navigation" class="mt-3 px-2">
					<ul class="pagination justify-content-end flex-wrap">
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
	</div>
</div>
