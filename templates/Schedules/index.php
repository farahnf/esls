<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Schedule> $schedules
 */
	use Cake\Routing\Router;
	echo $this->Html->css('select2/css/select2.css');
	echo $this->Html->script('select2/js/select2.full.min.js');
	echo $this->Html->css('jquery.datetimepicker.min.css');
	echo $this->Html->script('jquery.datetimepicker.full.js');
	echo $this->Html->script('https://cdn.jsdelivr.net/npm/apexcharts');
	echo $this->Html->css('fullcalendar/main.min.css');
	echo $this->Html->script('https://cdn.jsdelivr.net/npm/fullcalendar@6.1.18/index.global.min.js');
	echo $this->Html->script('https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js');
	$c_name = $this->request->getParam('controller');
	echo $this->Html->script('bootstrapModal', ['block' => 'scriptBottom']);
?>
<!-- Header -->
<div class="row text-body-secondary">
  <div class="col-10">
    <h1 class="my-0 page_title">My Schedule</h1>
    <h6 class="sub_title text-body-secondary"><?= h($system_name) ?></h6>
  </div>
</div>
<div class="line mb-4"></div>

<!-- Tabs -->
<div class="row">
  <div class="col-md-12">
    <ul class="nav nav-tabs nav-fill border-bottom mb-4">
      <li class="nav-item">
        <a class="nav-link active" data-bs-toggle="tab" href="#calendar-tab" role="tab">Calendar View</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#list-tab" role="tab">List View</a>
      </li>
       <li class="nav-item">
    <a class="nav-link" data-bs-toggle="tab" href="#pdf-tab">Download PDF</a>
  </li>
    </ul>

<!-- Calendar Filters: Only show on calendar and list tabs -->
<?php
  $currentTab = $this->request->getQuery('tab') ?? 'calendar';
  if ($currentTab !== 'pdf'):
?>
<div class="d-flex flex-wrap align-items-center justify-content-between mb-3 px-2" id="calendar-filters">
  <div class="d-flex gap-3 align-items-center flex-wrap">
    <div class="form-group mb-2">
      <label for="month-filter" class="form-label text-white fw-semibold mb-1">Month</label>
    <select id="month-filter" class="form-select form-select-sm bg-dark text-white border-secondary shadow-sm">
  <option value="" selected>All</option>
  <?php for ($m = 1; $m <= 12; $m++): ?>
    <option value="<?= str_pad($m, 2, '0', STR_PAD_LEFT) ?>"><?= date('F', mktime(0, 0, 0, $m, 10)) ?></option>
  <?php endfor; ?>
</select>
    </div>
    <div class="form-group mb-2">
      <label for="year-filter" class="form-label text-white fw-semibold mb-1">Year</label>
     <select id="year-filter" class="form-select form-select-sm bg-dark text-white border-secondary shadow-sm">
  <option value="" selected>All</option>
  <?php $currentYear = date('Y'); ?>
  <?php for ($y = $currentYear; $y >= $currentYear - 5; $y--): ?>
    <option value="<?= $y ?>"><?= $y ?></option>
  <?php endfor; ?>
</select>

    </div>
  </div>
</div>
<?php endif; ?>

<!-- Tab Contents -->
<div class="tab-content">
  <!-- ✅ Calendar View -->
  <div class="tab-pane fade show active" id="calendar-tab">
    <div class="calendar-wrapper d-flex flex-column align-items-center">
      <div class="legend-wrapper w-100 mb-3 px-2">
        <div class="d-flex gap-4">
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
      <div class="card bg-body-tertiary border-0 shadow mb-4 w-100">
        <div class="card-body text-center">
          <div id="calendar"></div>
        </div>
      </div>
    </div>
  </div>

  <!-- ✅ List View -->
  <div class="tab-pane fade" id="list-tab">
    <div class="card bg-body-tertiary border-0 shadow mb-4">
      <div class="card-body text-body-secondary">
        <div class="table-responsive">
          <table class="table table-sm table-border mb-4 table_transparent table-hover">
            <thead>
              <tr>
                <th class="text-center">Employee Name</th>
                <th class="text-center">Shift</th>
                <th class="text-center">Work Date</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($schedules as $schedule): ?>
              <tr>
                <td class="text-center"><?= h($schedule->employee->full_name ?? '') ?></td>
                <td class="text-center"><?= h($schedule->shift->shift_name ?? '') ?></td>
                <td class="text-center"><?= h($schedule->work_date->format('l, d M Y')) ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <div class="mt-3 px-2">
          <ul class="pagination justify-content-end flex-wrap">
            <?= $this->Paginator->first('<< ' . __('First')) ?>
            <?= $this->Paginator->prev('< ' . __('Previous')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('Next') . ' >') ?>
            <?= $this->Paginator->last(__('Last') . ' >>') ?>
          </ul>
          <div class="text-end">
            <?= $this->Paginator->counter(__('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')) ?>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ✅ Download PDF View -->
  <div class="tab-pane fade" id="pdf-tab">
    <div class="card bg-body-tertiary border-0 shadow mb-4">
      <div class="card-body">
        <form method="get" action="<?= $this->Url->build(['action' => 'pdfList']) ?>">
          <div class="row g-3 align-items-end">
            <div class="col-md-4">
              <label for="pdf-month" class="form-label">Month</label>
              <select name="month" id="pdf-month" class="form-select">
                <?php for ($m = 1; $m <= 12; $m++): ?>
                  <option value="<?= str_pad($m, 2, '0', STR_PAD_LEFT) ?>"><?= date('F', mktime(0, 0, 0, $m, 10)) ?></option>
                <?php endfor; ?>
              </select>
            </div>
            <div class="col-md-4">
              <label for="pdf-year" class="form-label">Year</label>
              <select name="year" id="pdf-year" class="form-select">
                <?php $currentYear = date('Y'); ?>
                <?php for ($y = $currentYear; $y >= $currentYear - 5; $y--): ?>
                  <option value="<?= $y ?>"><?= $y ?></option>
                <?php endfor; ?>
              </select>
            </div>
            <div class="col-md-4 text-start mt-2">
              <button type="submit" class="btn btn-dark">
                <i class="fa-solid fa-file-pdf me-1"></i> Generate PDF
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const calendarEl = document.getElementById('calendar');
  const monthFilter = document.getElementById('month-filter');
  const yearFilter = document.getElementById('year-filter');

  const events = <?= json_encode($calendarEvents) ?>;
  const holidayDates = <?= json_encode($publicHolidayDates ?? []) ?>;

  const holidayEvents = holidayDates.map(date => ({
    title: 'Public Holiday',
    start: date,
    display: 'background',
    backgroundColor: '#dc3545',
    overlap: false
  }));

  const calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'dayGridMonth',
    height: 'auto',
    events: [...events, ...holidayEvents],
    eventClick: function(info) {
      if (info.event.title === 'Public Holiday') {
        info.jsEvent.preventDefault();
      }
    }
  });

  calendar.render();

  function filterEvents() {
    const selectedMonth = monthFilter.value;
    const selectedYear = yearFilter.value;

    const hasFilter = selectedMonth || selectedYear;

    calendar.getEvents().forEach(event => {
      if (event.title === 'Public Holiday') return;

      if (!hasFilter) {
        event.setProp('display', 'auto');
        return;
      }

      const date = new Date(event.start);
      const eventMonth = (date.getMonth() + 1).toString().padStart(2, '0');
      const eventYear = date.getFullYear().toString();

      const isVisible =
        (!selectedMonth || eventMonth === selectedMonth) &&
        (!selectedYear || eventYear === selectedYear);

      event.setProp('display', isVisible ? 'auto' : 'none');
    });
  }

  if (monthFilter && yearFilter) {
    monthFilter.addEventListener('change', filterEvents);
    yearFilter.addEventListener('change', filterEvents);
  }
});
</script>



<!-- ✅ Styling -->
<style>
#calendar {
  width: 100%;
  max-width: 950px;
  font-size: 0.9rem;
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 8px;
  background-color: #444;
  color: white;
  margin: 0 auto;
}
#calendar-filters select {
  min-width: 120px;
  cursor: pointer;
}
#calendar-filters label {
  font-size: 0.85rem;
  margin-bottom: 0.25rem;
}
#calendar-filters .form-group {
  display: flex;
  flex-direction: column;
}
.legend-color {
  display: inline-block;
  width: 20px;
  height: 20px;
  border-radius: 4px;
  border: 1px solid #ddd;
}
.fc-toolbar-title {
  color: white !important;
  font-weight: bold;
  font-size: 1.5rem;
}
.legend-wrapper {
  max-width: 950px;
}
.select2-container {
  z-index: 999999 !important;
  color: #131414;
}
</style>