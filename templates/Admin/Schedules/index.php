<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Schedule> $schedules
 */
use Cake\Routing\Router;
use Cake\Datasource\ResultSetInterface;

echo $this->Html->css('select2/css/select2.css');
echo $this->Html->css('fullcalendar/main.min.css');
echo $this->Html->script('https://cdn.jsdelivr.net/npm/fullcalendar@6.1.18/index.global.min.js');
echo $this->Html->script('select2/js/select2.full.min.js');
echo $this->Html->css('jquery.datetimepicker.min.css');
echo $this->Html->script('jquery.datetimepicker.full.js');
echo $this->Html->script('https://cdn.jsdelivr.net/npm/apexcharts');
echo $this->Html->script('https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js');
$c_name = $this->request->getParam('controller');
echo $this->Html->script('bootstrapModal', ['block' => 'scriptBottom']);
echo $this->Html->script('https://code.jquery.com/jquery-3.6.0.min.js')
?>

<div class="row">
  <div class="col-md-12">
    <ul class="nav nav-tabs nav-fill border-bottom mb-4">
      <li class="nav-item">
        <a class="nav-link active" data-bs-toggle="tab" href="#calendar-tab" role="tab">Calendar</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#list-tab" role="tab">List View</a>
      </li>
       <li class="nav-item">
    <a class="nav-link" data-bs-toggle="tab" href="#pdf-tab">Download PDF</a>
  </li>
    </ul>

    <div class="tab-content px-0">

      <!-- Calendar Tab -->
      <div class="tab-pane fade show active" id="calendar-tab">
        <!-- Month and Year Filter -->
        <div class="row mb-3">
          <div class="col-md-3 offset-md-3">
            <select id="month-filter" class="form-select form-select-sm">
              <option value="">-- Month --</option>
              <?php for ($m = 1; $m <= 12; $m++): 
                $val = str_pad($m, 2, '0', STR_PAD_LEFT); ?>
                <option value="<?= $val ?>"><?= date('F', mktime(0, 0, 0, $m, 1)) ?></option>
              <?php endfor; ?>
            </select>
          </div>
          <div class="col-md-3">
            <select id="year-filter" class="form-select form-select-sm">
              <option value="">-- Year --</option>
              <?php for ($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                <option value="<?= $y ?>"><?= $y ?></option>
              <?php endfor; ?>
            </select>
          </div>
        </div>

 <!-- Shift Legend -->
      <div class="mb-3">
        <div class="d-flex gap-4 align-items-center bg-dark p-3 rounded shadow-sm" style="max-width: 500px;">
          <div class="d-flex align-items-center gap-2">
            <span class="legend-color" style="background-color: #28a745;"></span> <span class="text-white">Morning Shift</span>
          </div>
          <div class="d-flex align-items-center gap-2">
            <span class="legend-color" style="background-color: #ffc107;"></span> <span class="text-white">Evening Shift</span>
          </div>
        </div>
      </div>

        <!-- Calendar -->
        <div class="d-flex justify-content-center">
          <div id="calendar"></div>
        </div>
      </div>

      <!-- List View Tab -->
      <div class="tab-pane fade" id="list-tab" role="tabpanel">
        <!-- Filter Form -->
        <div class="mb-4">
          <?= $this->Form->create(null, ['type' => 'get', 'class' => 'row g-3 align-items-end']) ?>
            <div class="col-md-3">
              <?= $this->Form->control('start_date', ['label' => 'Start Date', 'type' => 'date', 'class' => 'form-control form-control-sm', 'value' => $this->request->getQuery('start_date')]) ?>
            </div>
            <div class="col-md-3">
              <?= $this->Form->control('end_date', ['label' => 'End Date', 'type' => 'date', 'class' => 'form-control form-control-sm', 'value' => $this->request->getQuery('end_date')]) ?>
            </div>
            <div class="col-md-3">
              <?= $this->Form->button('Filter', ['class' => 'btn btn-sm btn-outline-primary']) ?>
              <?= $this->Html->link('Reset', ['action' => 'index'], ['class' => 'btn btn-sm btn-outline-secondary ms-2']) ?>
            </div>
          <?= $this->Form->end() ?>
        </div>

        <!-- Table -->
        <div class="table-responsive mt-4">
          <table class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>Employee</th>
                <th>Shift</th>
                <th>Date</th>
                <th class="text-center">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($schedules as $schedule): ?>
              <tr>
                <td><?= h($schedule->employee->full_name ?? 'N/A') ?></td>
                <td><?= h($schedule->shift->shift_name ?? 'N/A') ?></td>
                <td><?= h($schedule->work_date ?? '-') ?></td>
                <td class="text-center">
                  <button class="btn btn-sm btn-primary openEditModal"
                    data-id="<?= h($schedule->schedule_id) ?>"
                    data-employee-id="<?= h($schedule->employee_id) ?>"
                    data-shift-id="<?= h($schedule->shift_id) ?>">
                    <i class="fas fa-edit"></i> Edit
                  </button>
                  <?= $this->Form->postLink(
                    '<i class="fas fa-trash-alt"></i> Delete',
                    ['action' => 'deleteFromCalendar', $schedule->schedule_id],
                    ['confirm' => 'Are you sure?', 'class' => 'btn btn-sm btn-danger', 'escape' => false]
                  ) ?>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

       <!-- Pagination Summary and Controls (only for List View) -->
<div class="mt-2 mb-3 small text-muted text-center">
  <?= $this->Paginator->counter('{{start}} to {{end}} of {{count}} schedules') ?>
</div>

<nav aria-label="Pagination">
  <ul class="pagination pagination-sm justify-content-center">
    <li class="page-item"><?= $this->Paginator->first('<<', ['class' => 'page-link']) ?></li>
    <li class="page-item"><?= $this->Paginator->prev('<', ['class' => 'page-link']) ?></li>
    
  <?= $this->Paginator->counter() ?>
<?= $this->Paginator->first('<<') ?>
<?= $this->Paginator->prev('<') ?>
<?= $this->Paginator->numbers() ?>
<?= $this->Paginator->next('>') ?>
<?= $this->Paginator->last('>>') ?>
    
    <li class="page-item"><?= $this->Paginator->next('>', ['class' => 'page-link']) ?></li>
    <li class="page-item"><?= $this->Paginator->last('>>', ['class' => 'page-link']) ?></li>
  </ul>
</nav>
  </div>
</div>

<!-- PDF Download View -->
<div class="tab-pane fade" id="pdf-tab">
  <div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-dark text-white">
      <h5 class="mb-0"><i class="fa-solid fa-file-pdf me-2"></i>Download Monthly Schedule PDF</h5>
    </div>

    <div class="card-body">
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



<!-- Edit Modal -->
<div class="modal fade" id="editScheduleModal" tabindex="-1" aria-labelledby="editScheduleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <?= $this->Form->create(null, ['url' => ['action' => 'editFromCalendar'], 'id' => 'editScheduleForm']) ?>
        <div class="modal-header">
          <h5 class="modal-title">Edit Assigned Schedule</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <?= $this->Form->hidden('schedule_id', ['id' => 'edit-schedule-id']) ?>
          <div class="mb-3">
            <?= $this->Form->control('employee_id', ['label' => 'Employee', 'options' => $employees, 'class' => 'form-select select2', 'id' => 'edit-employee-id']) ?>
          </div>
          <div class="mb-3">
            <?= $this->Form->control('shift_id', ['label' => 'Shift', 'options' => $shifts, 'class' => 'form-select select2', 'id' => 'edit-shift-id']) ?>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <?= $this->Form->button('Save Changes', ['class' => 'btn btn-primary']) ?>
        </div>
      <?= $this->Form->end() ?>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');
    const events = <?= json_encode($calendarEvents ?? []) ?>;
    const holidayDates = <?= json_encode($publicHolidayDates ?? []) ?>;

    const calendar = new FullCalendar.Calendar(calendarEl, {
      initialView: 'dayGridMonth',
      editable: true,
      selectable: true,
      height: "auto",
      headerToolbar: {
        left: 'prev,next',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay'
      },
      events: [
        ...events,
        ...holidayDates.map(date => ({
          title: 'Public Holiday',
          start: date,
          display: 'background',
          backgroundColor: '#dc3545' // red
        }))
      ],

      dateClick: function (info) {
        if (holidayDates.includes(info.dateStr)) {
          alert("This is a public holiday. Assignment not allowed.");
          return;
        }

        document.getElementById('work-date').value = info.dateStr;
        $('#conflict-warning').hide();
        const modal = new bootstrap.Modal(document.getElementById('scheduleModal'));
        modal.show();
      },

      eventDrop: function (info) {
        if (holidayDates.includes(info.event.startStr)) {
          alert("You cannot move the schedule to a public holiday.");
          info.revert();
          return;
        }

        fetch('/admin/schedules/updateDate/' + info.event.id, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': '<?= $this->request->getAttribute("csrfToken") ?>'
          },
          body: JSON.stringify({ new_date: info.event.startStr })
        }).then(response => {
          if (!response.ok) {
            alert("Failed to update schedule.");
            info.revert();
          } else {
            calendar.refetchEvents();
          }
        });
      },

      eventClick: function (info) {
        const id = info.event.id;
        const employeeId = info.event.extendedProps.employee_id;
        const shiftId = info.event.extendedProps.shift_id;

        $('#edit-schedule-id').val(id);
        $('#edit-employee-id').val(employeeId).trigger('change');
        $('#edit-shift-id').val(shiftId).trigger('change');

        const modal = new bootstrap.Modal(document.getElementById('editScheduleModal'));
        modal.show();
      }
    });

    calendar.render();

    // Add Schedule - Assign Button
    $('#assignBtn').on('click', function (e) {
      e.preventDefault();
      const selectedEmployeeId = $('#employee-id').val();
      const selectedDate = $('#work-date').val();
      const selectedDayName = new Date(selectedDate).toLocaleDateString('en-US', { weekday: 'long' });

      const restDayMap = <?= json_encode($employeesRestDays ?? []) ?>;
      const employeeRestDay = restDayMap[selectedEmployeeId];

      if (holidayDates.includes(selectedDate)) {
        alert("This is a public holiday. You cannot assign a shift.");
        return;
      }

      if (employeeRestDay === selectedDayName) {
        alert("This employee's rest day is " + employeeRestDay + ". Please choose another date or employee.");
        return;
      }

      const form = $('#addScheduleForm');
      $.ajax({
        type: 'POST',
        url: form.attr('action'),
        data: form.serialize(),
        headers: {
          'X-CSRF-Token': '<?= $this->request->getAttribute("csrfToken") ?>'
        },
        success: function (response) {
          alert('Schedule assigned successfully.');
          form[0].reset();
          calendar.refetchEvents();
        },
        error: function () {
          alert('Failed to assign schedule.');
        }
      });
    });

    // Edit Schedule Modal Submission
    $('#editScheduleForm').on('submit', function (e) {
      e.preventDefault();
      const form = $(this);

      $.ajax({
        type: 'POST',
        url: form.attr('action'),
        data: form.serialize(),
        headers: {
          'X-CSRF-Token': '<?= $this->request->getAttribute("csrfToken") ?>'
        },
        success: function (response) {
          if (response.success) {
            alert(response.message || 'Schedule updated successfully.');
            const modal = bootstrap.Modal.getInstance(document.getElementById('editScheduleModal'));
            modal.hide();
            form[0].reset();
            calendar.refetchEvents();
          } else {
            alert(response.message || 'Failed to update schedule.');
          }
        },
        error: function () {
          alert('An error occurred while updating the schedule.');
        }
      });
    });

    // Month & Year Filter
    $('#month-filter, #year-filter').on('change', function () {
      const selectedMonth = $('#month-filter').val();
      const selectedYear = $('#year-filter').val();

      calendar.getEvents().forEach(event => {
        const date = new Date(event.start);
        const month = (date.getMonth() + 1).toString().padStart(2, '0');
        const year = date.getFullYear().toString();
        const visible = (!selectedMonth || month === selectedMonth) && (!selectedYear || year === selectedYear);
        event.setProp('display', visible ? 'auto' : 'none');
      });
    });

    // List View Edit Button Handler
    $(document).on('click', '.openEditModal', function () {
      const id = $(this).data('id');
      const employeeId = $(this).data('employee-id');
      const shiftId = $(this).data('shift-id');
      $('#edit-schedule-id').val(id);
      $('#edit-employee-id').val(employeeId).trigger('change');
      $('#edit-shift-id').val(shiftId).trigger('change');
      const modal = new bootstrap.Modal(document.getElementById('editScheduleModal'));
      modal.show();
    });

    // List Tab Persistence After Pagination
    if (window.location.hash === "#list-tab") {
      const tab = new bootstrap.Tab(document.querySelector('a[href="#list-tab"]'));
      tab.show();
    }

    document.querySelectorAll(".pagination a").forEach(function (link) {
      link.href += "#list-tab";
    });

    // Init Select2
    $('.select2').select2({
      dropdownParent: $('#editScheduleModal'),
      theme: 'bootstrap-5',
      width: '100%'
    });
  });
</script>


<style>
#calendar {
  width: 100%;
  max-width: 950px;
  font-size: 1rem;
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 8px;
  background-color: #444;
  color: white;
}

/* Select2 styles */
.select2-container {
  z-index: 999999 !important;
  color: #131414;
}
.select2-container--bootstrap-5 .select2-selection--single {
  height: 38px;
  padding: 0.375rem 2.25rem 0.375rem 0.75rem;
  font-size: 1.1rem;
  color: #131414;
  line-height: 1.5;
  background-color: rgb(118, 120, 121);
  border: 1px solid rgb(135, 163, 190);
  border-radius: 0.375rem;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='%23666' viewBox='0 0 4 5'%3E%3Cpath d='M2 0L0 2h4L2 0z'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 0.75rem center;
  background-size: 0.65em auto;
}
.select2-container--bootstrap-5 .select2-selection--single .select2-selection__arrow {
  display: none !important;
}

/* FullCalendar header */
.fc-toolbar-title {
  color: white !important;
  font-weight: bold;
  font-size: 1.5rem;
}

/* Tabs */
.nav-tabs {
  justify-content: center !important;
}

/* Legend blocks */
.legend-color {
  display: inline-block;
  width: 20px;
  height: 20px;
  border-radius: 4px;
  border: 1px solid #ddd;
}

/* Pagination */
.pagination .page-link {
  color: #007bff;
}
.pagination .active .page-link {
  background-color: #007bff;
  border-color: #007bff;
  color: white;
}

/* ✅ Let FullCalendar apply color from PHP */
.fc-event, .fc-event-dot {
  color: #fff !important;
  font-weight: 800;
  border: none !important;
  border-radius: 4px;
  padding: 2px 6px;
  /* DO NOT override background-color here! Let FullCalendar use 'color' key */
}
</style>
