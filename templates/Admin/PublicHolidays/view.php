<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\PublicHoliday $publicHoliday
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
							<li><?= $this->Html->link(__('Edit Public Holiday'), ['action' => 'edit', $publicHoliday->holiday_id], ['class' => 'dropdown-item', 'escapeTitle' => false]) ?></li>
				<li><?= $this->Form->postLink(__('Delete Public Holiday'), ['action' => 'delete', $publicHoliday->holiday_id], ['confirm' => __('Are you sure you want to delete # {0}?', $publicHoliday->holiday_id), 'class' => 'dropdown-item', 'escapeTitle' => false]) ?></li>
				<li><hr class="dropdown-divider"></li>
				<li><?= $this->Html->link(__('List Public Holidays'), ['action' => 'index'], ['class' => 'dropdown-item', 'escapeTitle' => false]) ?></li>
				<li><?= $this->Html->link(__('New Public Holiday'), ['action' => 'add'], ['class' => 'dropdown-item', 'escapeTitle' => false]) ?></li>
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
            <h3><?= h($publicHoliday->holiday_id) ?></h3>
    <div class="table-responsive">
        <table class="table">
                <tr>
                    <th><?= __('Description') ?></th>
                    <td><?= h($publicHoliday->description) ?></td>
                </tr>
                <tr>
                    <th><?= __('Holiday Id') ?></th>
                    <td><?= $this->Number->format($publicHoliday->holiday_id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Holiday Date') ?></th>
                    <td><?= h($publicHoliday->holiday_date) ?></td>
                </tr>
                <tr>
                    <th><?= __('Created') ?></th>
                    <td><?= h($publicHoliday->created) ?></td>
                </tr>
                <tr>
                    <th><?= __('Modified') ?></th>
                    <td><?= h($publicHoliday->modified) ?></td>
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




