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

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />

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
							<li><?= $this->Html->link(__('<i class="fa-solid fa-plus"></i> New Leave'), ['action' => 'add'], ['class' => 'dropdown-item', 'escapeTitle' => false]) ?></li>
							</div>
		</div>
</div>
</div>
<div class="line mb-10"></div>
<!--/Header-->
<div class="row">
	<div class="col-md-9">
		<!-- Nav tabs -->
		<div class="nav-align-top mb-8">
			<ul class="nav nav-tabs nav-fill border-bottom mb-8" role="tablist">
				<li class="nav-item">
					<a class="nav-link active" data-bs-toggle="tab" href="#list"><i class="fa-solid fa-bars-staggered"></i> List</a>
				</li>
				<li class="nav-item">
			<a class="nav-link" data-bs-toggle="tab" href="#pdf-list">
				<i class="fa-solid fa-file-pdf"></i> Download PDF
			</a>
		</li>
			</ul>
		</div>
		
		
		<div class="tab-content px-0">
		<div class="tab-pane fade active show" id="list">
    <div class="card bg-body-tertiary border-0 shadow mb-8">
	<div class="card-body text-body-secondary">

	<!-- Tab panes -->
	<div class="table-responsive">
        <table class="table table-sm table-border mb-8 table_transparent table-hover">
            <thead>
		<?php
			$page = $this->Paginator->counter('{{page}}');
			$limit = 10; 
			$counter = ($page * $limit) - $limit + 1;
		?>
                <tr>
                    <th><?= $this->Paginator->sort('employee_id') ?></th>
                    <th><?= $this->Paginator->sort('leave_type_id') ?></th>
                    <th><?= $this->Paginator->sort('start_date') ?></th>
                    <th><?= $this->Paginator->sort('end_date') ?></th>
                    <th><?= $this->Paginator->sort('status') ?></th>
                    <th><?= $this->Paginator->sort('applied_on') ?></th>
                </tr>
            </thead>
		
           <tbody>
    <?php foreach ($leaves as $leave): ?>
    <tr>
        <td>
            <?= $leave->has('employee') ? h($leave->employee->full_name) : '-' ?>
        </td>
        <td>
            <?= $leave->has('leave_type') ? h($leave->leave_type->leave_type_name) : '-' ?>
        </td>
        <td><?= h($leave->start_date) ?></td>
        <td><?= h($leave->end_date) ?></td>
     <td>
    <?php
        // Assign class based on status
        $statusClass = match ($leave->status) {
            'Approved' => 'bg-success text-white',
            'Rejected' => 'bg-danger text-white',
            'Pending'  => 'bg-warning text-dark',
            default    => ''
        };
    ?>

    <?= $this->Form->create(null, [
        'url' => ['prefix' => 'Admin', 'controller' => 'Leaves', 'action' => 'updateStatus'],
        'type' => 'post',
        'class' => 'd-flex align-items-center gap-2' // Bootstrap flex layout with spacing
    ]) ?>
        <?= $this->Form->hidden('leave_id', ['value' => $leave->leave_id]) ?>
        <?= $this->Form->control('status', [
            'label' => false,
            'options' => ['Pending' => 'Pending', 'Approved' => 'Approved', 'Rejected' => 'Rejected'],
            'value' => $leave->status,
            'class' => 'form-select form-select-sm ' . $statusClass
        ]) ?>
        <?= $this->Form->button('Update', ['class' => 'btn btn-sm btn-primary']) ?>
    <?= $this->Form->end() ?>
</td>


        <td><?= h($leave->applied_on) ?></td>
    </tr>
    <?php endforeach; ?>
</tbody>

        </table>
    </div>

<!----------------Pagination--------------->

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

	<!-- PDF Download View -->
<div class="tab-pane fade px-0" id="pdf-list">
  <div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-dark text-white">
      <h5 class="mb-0"><i class="fa-solid fa-file-pdf me-2"></i>Download Monthly Schedule PDF</h5>
    </div>

    <div class="card-body">
      <form method="get" action="<?= $this->Url->build(['action' => 'pdfList']) ?>">
        <div class="row g-3 align-items-end">


         <!-- Filter Form -->
<div class="card p-3 mb-4 shadow-sm border-0">
  <form method="get" action="<?= $this->Url->build(['action' => 'pdfList']) ?>">
    <div class="row g-3 align-items-end">
      
      <!-- Month Filter -->
      <div class="col-md-6 col-lg-4">
        <label for="pdf-month" class="form-label fw-semibold">Select Month</label>
        <select name="month" id="pdf-month" class="form-select shadow-sm">
          <?php for ($m = 1; $m <= 12; $m++): ?>
            <option value="<?= str_pad($m, 2, '0', STR_PAD_LEFT) ?>">
              <?= date('F', mktime(0, 0, 0, $m, 1)) ?>
            </option>
          <?php endfor; ?>
        </select>
      </div>

      <!-- Year Filter -->
      <div class="col-md-6 col-lg-4">
        <label for="pdf-year" class="form-label fw-semibold">Select Year</label>
        <select name="year" id="pdf-year" class="form-select shadow-sm">
          <?php $currentYear = date('Y'); ?>
          <?php for ($y = $currentYear; $y >= $currentYear - 5; $y--): ?>
            <option value="<?= $y ?>"><?= $y ?></option>
          <?php endfor; ?>
        </select>
      </div>

      <!-- Submit Button -->
     <div class="col-12 col-lg-4 mt-3 mt-lg-0 d-flex align-items-end">
  <button type="submit" class="btn btn-danger px-2 py-1 shadow-sm" style="font-size: 0.8rem;">
    <i class="fa-solid fa-file-pdf me-1" style="font-size: 0.85rem;"></i> Generate PDF
  </button>
</div>

    </div>
  </form>
</div>

  </div>
</div>

<script>
const ctx = document.getElementById('monthly');
const monthly = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($monthArray); ?>,
        datasets: [{
            label: '# of Leaves(s)',
			data: <?php echo json_encode($countArray); ?>,
            backgroundColor: [
                'rgba(255, 99, 132, 0.2)','rgba(54, 162, 235, 0.2)','rgba(255, 206, 86, 0.2)','rgba(75, 192, 192, 0.2)','rgba(153, 102, 255, 0.2)','rgba(89, 233, 28, 0.2)','rgba(255, 5, 5, 0.2)','rgba(255, 128, 0, 0.2)','rgba(153, 153, 153, 0.2)','rgba(15, 207, 210, 0.2)','rgba(44, 13, 181, 0.2)','rgba(86, 172, 12, 0.2)'
            ],
            borderColor: [
                'rgba(255, 99, 132, 1)','rgba(54, 162, 235, 1)','rgba(255, 206, 86, 1)','rgba(75, 192, 192, 1)','rgba(153, 102, 255, 1)','rgba(89, 233, 28, 1)','rgba(255, 5, 5, 1)','rgba(255, 128, 0, 1)','rgba(153, 153, 153, 1)','rgba(15, 207, 210, 1)','rgba(44, 13, 181, 1)','rgba(86, 172, 12, 1)'
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
                text: 'Leaves (Monthly)',
				font: {
				  size: 15
					}
				},
			subtitle: {
                display: false,
                text: '<?php echo $system_name; ?>'
				},
			legend: {
					display: false,
					labels: {
						color: 'rgb(255, 99, 132)'
					}
				},
        }
    }
});
</script>
		</div>
	</div>
	</div>
	
		</div>
	</div>
	</div>
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