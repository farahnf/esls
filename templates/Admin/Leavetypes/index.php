<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Leavetype> $leavetypes
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
    <div class="dropdown mx-3 mt-2">
      <button class="btn p-0 border-0" type="button" id="dropdownMenu" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fa-solid fa-bars text-primary"></i>
      </button>
      <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenu">
        <li><?= $this->Html->link(__('<i class="fa-solid fa-plus"></i> New Leavetype'), ['action' => 'add'], ['class' => 'dropdown-item', 'escapeTitle' => false]) ?></li>
      </ul>
    </div>
  </div>
</div>
<div class="line mb-4"></div>
<!--/Header-->

<div class="row">
  <div class="col-md-9">
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
                <th>#</th>
                <th><?= $this->Paginator->sort('leave_type_id') ?></th>
                <th><?= $this->Paginator->sort('leave_type_name') ?></th>
                <th><?= __('Actions') ?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($leavetypes as $leavetype): ?>
              <tr>
                <td><?= $counter++ ?></td>
                <td><?= $this->Number->format($leavetype->leave_type_id) ?></td>
                <td><?= h($leavetype->leave_type_name) ?></td>
                <td class="text-center">
                  <div class="btn-group shadow" role="group">
                    <?= $this->Html->link(__('<i class="far fa-folder-open"></i>'), ['action' => 'view', $leavetype->leave_type_id], ['class' => 'btn btn-outline-primary btn-xs', 'escapeTitle' => false]) ?>
                    <?= $this->Html->link(__('<i class="fa-regular fa-pen-to-square"></i>'), ['action' => 'edit', $leavetype->leave_type_id], ['class' => 'btn btn-outline-warning btn-xs', 'escapeTitle' => false]) ?>
                    <?= $this->Form->postLink(__('<i class="fa-regular fa-trash-can"></i>'), ['action' => 'delete', $leavetype->leave_type_id], [
                      'confirm' => __('Are you sure you want to delete Leavetype: "{0}"?', $leavetype->leave_type_name),
                      'class' => 'btn btn-outline-danger btn-xs',
                      'escapeTitle' => false
                    ]) ?>
                  </div>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <div class="mt-3 px-2">
          <ul class="pagination justify-content-end flex-wrap">
            <?= $this->Paginator->first('<< ' . __('First')) ?>
            <?= $this->Paginator->prev('< ' . __('Previous')) ?>
            <?= $this->Paginator->numbers(['before' => '', 'after' => '']) ?>
            <?= $this->Paginator->next(__('Next') . ' >') ?>
            <?= $this->Paginator->last(__('Last') . ' >>') ?>
          </ul>
          <div class="text-end">
            <?= $this->Paginator->counter(__('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')) ?>
          </div>
        </div>
      </div>
    </div>

    <!-- Export Tabs -->
    <div class="card bg-body-tertiary border-0 shadow mb-4">
      <div class="card-body">
        <div class="card-title mb-3">Export</div>
        <?php $baseUrl = Router::url('/', true) . 'leavetypes'; ?>
        <div class="d-flex gap-3">
          <a href="<?= $baseUrl ?>/csv" class="btn btn-outline-primary"><i class="fa-solid fa-file-csv"></i> CSV</a>
          <a href="<?= $baseUrl ?>/json" target="_blank" class="btn btn-outline-warning"><i class="fa-solid fa-code"></i> JSON</a>
          <a href="<?= $baseUrl ?>/pdfList" class="btn btn-outline-danger"><i class="fa-solid fa-file-pdf"></i> PDF</a>
        </div>
      </div>
    </div>
  </div>

  <!-- Right Sidebar -->
  <div class="col-md-3">
    <div class="card bg-body-tertiary border-0 shadow mb-4">
      <div class="card-body">
        <div class="card-title mb-3">Search</div>
        <?= $this->Form->create(null, ['valueSources' => 'query', 'url' => ['controller' => 'Leavetypes', 'action' => 'index']]) ?>
        <fieldset>
          <?= $this->Form->control('id', ['required' => false]) ?>
        </fieldset>
        <div class="text-end mt-3">
          <?= $this->Form->button(__('Search'), ['class' => 'btn btn-outline-primary btn-sm']) ?>
          <?= $this->Html->link(__('Reset'), ['action' => 'index'], ['class' => 'btn btn-outline-warning btn-sm']) ?>
        </div>
        <?= $this->Form->end() ?>
      </div>
    </div>
  </div>
</div>
