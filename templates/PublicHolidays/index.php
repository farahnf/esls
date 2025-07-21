<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\PublicHoliday> $publicHolidays
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
		<h1 class="my-0 page_title"><?php echo $title; ?></h1>
		<h6 class="sub_title text-body-secondary"><?php echo $system_name; ?></h6>
	</div>
	<div class="col-2 text-end">
		<div class="dropdown mx-3 mt-2">
			<button class="btn p-0 border-0" type="button" id="orederStatistics" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			<i class="fa-solid fa-bars text-primary"></i>
			</button>
				<div class="dropdown-menu dropdown-menu-end" aria-labelledby="orederStatistics">
							<li><?= $this->Html->link(__('<i class="fa-solid fa-plus"></i> New Public Holiday'), ['action' => 'add'], ['class' => 'dropdown-item', 'escapeTitle' => false]) ?></li>
							</div>
		</div>
</div>
</div>
<div class="line mb-4"></div>
<!--/Header-->
<div class="row">
	<div class="col-md-9">
		<!-- Nav tabs -->
		<div class="nav-align-top mb-4">
			<ul class="nav nav-tabs nav-fill border-bottom mb-4" role="tablist">
				<li class="nav-item">
					<a class="nav-link active" data-bs-toggle="tab" href="#list"><i class="fa-solid fa-bars-staggered"></i> List</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" data-bs-toggle="tab" href="#export"><i class="fa-solid fa-download"></i> Export</a>
				</li>
			</ul>
		</div>
		
		
		<div class="tab-content px-0">
		<div class="tab-pane fade active show" id="list">
    <div class="card bg-body-tertiary border-0 shadow mb-4">
	<div class="card-body text-body-secondary">
	<!-- Tab panes -->
	<div class="table-responsive">
        <table class="table table-sm table-border mb-4 table_transparent table-hover">
            <thead>
		<?php
			$page = $this->Paginator->counter('{{page}}');
			$limit = 10; 
			$counter = ($page * $limit) - $limit + 1;
		?>
                <tr>
                    <th><?= $this->Paginator->sort('holiday_date') ?></th>
                    <th><?= $this->Paginator->sort('description') ?></th>
                    <th><?= $this->Paginator->sort('modified') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($publicHolidays as $publicHoliday): ?>
                <tr>
                    <td><?= h($publicHoliday->holiday_date) ?></td>
                    <td><?= h($publicHoliday->description) ?></td>
                    <td><?= h($publicHoliday->modified) ?></td>
					<td class="actions text-center">
						<div class="btn-group shadow" role="group" aria-label="Basic example">
							<?= $this->Html->link(__('<i class="far fa-folder-open"></i>'), ['action' => 'view', $publicHoliday->holiday_id], ['class' => 'btn btn-outline-primary btn-xs', 'escapeTitle' => false]) ?>
							<?= $this->Html->link(__('<i class="fa-regular fa-pen-to-square"></i>'), ['action' => 'edit', $publicHoliday->holiday_id], ['class' => 'btn btn-outline-warning btn-xs', 'escapeTitle' => false]) ?>
							<?php $this->Form->setTemplates([
								'confirmJs' => 'addToModal("{{formName}}"); return false;'
							]); ?>
							<?= $this->Form->postLink(
								__('<i class="fa-regular fa-trash-can"></i>'),
								['action' => 'delete', $publicHoliday->holiday_id],
								[
									'confirm' => __('Are you sure you want to delete Public Holidays: "{0}"?', $publicHoliday->holiday_id),
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
		
		

	</div>
</div>
</div>

<div class="modal" id="bootstrapModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
			<i class="fa-regular fa-circle-xmark fa-6x text-danger mb-3"></i>
                <p id="confirmMessage"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="ok">OK</button>
            </div>
        </div>
    </div>
</div>