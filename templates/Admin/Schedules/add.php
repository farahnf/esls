<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Schedule $schedule
 * @var \Cake\Collection\CollectionInterface|string[] $employees
 * @var \Cake\Collection\CollectionInterface|string[] $shifts
 */
use Cake\Routing\Router;

// CSS and JS includes
echo $this->Html->css('select2/css/select2.css');
echo $this->Html->css('jquery.datetimepicker.min.css');
echo $this->Html->css('fullcalendar/main.min.css');
echo $this->Html->script('select2/js/select2.full.min.js');
echo $this->Html->script('jquery.datetimepicker.full.js');
echo $this->Html->script('https://cdn.jsdelivr.net/npm/fullcalendar@6.1.18/index.global.min.js');
?>

<!-- Existing Head -->
<?= $this->Html->css('select2/css/select2.min.css') ?>
<?= $this->Html->script('select2/js/select2.full.min.js') ?>


<div class="row text-body-secondary mb-3">
  <div class="col-10">
    <h1 class="page_title">Manage Employee Schedules</h1>
    <h6 class="sub_title"><?= h($system_name) ?></h6>
  </div>
  <div class="col-2 text-end">
    <div class="dropdown">
      <button class="btn border-0" type="button" data-bs-toggle="dropdown">
        <i class="fa-solid fa-bars text-primary"></i>
      </button>
      <ul class="dropdown-menu dropdown-menu-end">
        <li><?= $this->Html->link('Back to Schedule List', ['action' => 'index'], ['class' => 'dropdown-item']) ?></li>
      </ul>
    </div>
  </div>
</div>

<!-- Shift Legend -->
<div class="mb-3">
  <div class="d-flex gap-4 align-items-center bg-dark p-3 rounded shadow-sm" style="max-width: 500px;">
    <div class="d-flex align-items-center gap-2">
      <span class="legend-color" style="background-color: #28a745;"></span>
      <span class="text-white">Morning Shift</span>
    </div>
    <div class="d-flex align-items-center gap-2">
      <span class="legend-color" style="background-color: #ffc107;"></span>
      <span class="text-white">Evening Shift</span>
    </div>
  </div>
</div>

<!-- Calendar -->
<div class="card border-0 shadow-sm">
  <div class="card-body">
    <div id="calendar"></div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="scheduleModal" tabindex="-1" aria-labelledby="scheduleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <?= $this->Form->create($schedule, ['url' => ['action' => 'add'], 'id' => 'scheduleForm']) ?>
      <div class="modal-header">
        <h5 class="modal-title">Assign Employee to Shift</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <?= $this->Form->hidden('work_date', ['id' => 'work-date']) ?>
        <div class="mb-3">
          <?= $this->Form->control('employee_id', ['label' => 'Employee', 'options' => $employees, 'class' => 'form-select select2', 'empty' => '-- Choose --']) ?>
        </div>
        <div class="mb-3">
          <?= $this->Form->control('shift_id', ['label' => 'Shift', 'options' => $shifts, 'class' => 'form-select select2', 'empty' => '-- Choose --']) ?>
        </div>
      </div>
      <div class="modal-footer">
        <span class="text-danger small me-auto" id="conflict-warning" style="display:none;">This employee is already assigned on this day.</span>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <?= $this->Form->button('Assign', ['class' => 'btn btn-primary', 'id' => 'assignBtn']) ?>
      </div>
      <?= $this->Form->end() ?>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const calendarEl = document.getElementById('calendar');
  const events = <?= json_encode($calendarEvents ?? []) ?>;

  const calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'dayGridMonth',
    selectable: true,
    height: 'auto',
    headerToolbar: {
      left: 'prev,next',
      center: 'title',
      right: 'dayGridMonth,timeGridWeek'
    },
    dateClick: function (info) {
      $('#work-date').val(info.dateStr);
      $('#conflict-warning').hide();
      const modal = new bootstrap.Modal(document.getElementById('scheduleModal'));
      modal.show();
    },
    events: events,
  });

  calendar.render();

  // Initialize Select2 inside modal
  $('.select2').select2({
    dropdownParent: $('#scheduleModal'),
    theme: 'bootstrap-5',
    width: '100%',
    minimumResultsForSearch: 5
  });

  // Conflict prevention on assign
  $('#assignBtn').on('click', function (e) {
    const selectedEmployeeId = $('#employee-id').val();
    const selectedDate = $('#work-date').val();
    const hasConflict = events.some(event =>
      event.extendedProps.employee_id == selectedEmployeeId && event.start.startsWith(selectedDate)
    );

    if (hasConflict) {
      e.preventDefault();
      $('#conflict-warning').show();
    }
  });
});
</script>

<style>
#calendar {
  max-width: 850px;
  margin: 10px auto;
  font-size: 0.9rem;
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 8px;
  background-color: #444;
  color: white;
}
.select2-container {
  z-index: 9999 !important;
  color: #131414;
}
.select2-container--bootstrap-5 .select2-selection--single {
  height: 38px;
  padding: 0.375rem 2.25rem 0.375rem 0.75rem;
  font-size: 1rem;
  background-color: rgb(118, 120, 121);
  border: 1px solid rgb(135, 163, 190);
  border-radius: 0.375rem;
}
.select2-container--bootstrap-5 .select2-selection--single .select2-selection__arrow {
  display: none !important;
}
.fc-toolbar-title {
  color: white !important;
  font-weight: bold;
  font-size: 1.5rem;
}
.legend-color {
  display: inline-block;
  width: 20px;
  height: 20px;
  border-radius: 4px;
  border: 1px solid #ccc;
}
.fc-event, .fc-event-dot {
  color: #fff !important;
  font-weight: 800;
  border: none !important;
  border-radius: 4px;
  padding: 2px 6px;
}
</style>