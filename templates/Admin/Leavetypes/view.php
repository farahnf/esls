<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Leavetype $leavetype
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
							<li><?= $this->Html->link(__('Edit Leavetype'), ['action' => 'edit', $leavetype->leave_type_id], ['class' => 'dropdown-item', 'escapeTitle' => false]) ?></li>
				<li><?= $this->Form->postLink(__('Delete Leavetype'), ['action' => 'delete', $leavetype->leave_type_id], ['confirm' => __('Are you sure you want to delete # {0}?', $leavetype->leave_type_id), 'class' => 'dropdown-item', 'escapeTitle' => false]) ?></li>
				<li><hr class="dropdown-divider"></li>
				<li><?= $this->Html->link(__('List Leavetypes'), ['action' => 'index'], ['class' => 'dropdown-item', 'escapeTitle' => false]) ?></li>
				<li><?= $this->Html->link(__('New Leavetype'), ['action' => 'add'], ['class' => 'dropdown-item', 'escapeTitle' => false]) ?></li>
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
            <h3><?= h($leavetype->leave_type_name) ?></h3>
    <div class="table-responsive">
        <table class="table">
                <tr>
                    <th><?= __('Leave Type Name') ?></th>
                    <td><?= h($leavetype->leave_type_name) ?></td>
                </tr>
                <tr>
                    <th><?= __('Leave Type Id') ?></th>
                    <td><?= $this->Number->format($leavetype->leave_type_id) ?></td>
                </tr>
            </table>
            </div>

			</div>
		</div>
		

            
            


		
	</div>
	<div class="col-md-3">
	  Column
	</div>
</div>




