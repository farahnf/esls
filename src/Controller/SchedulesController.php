<?php
declare(strict_types=1);

namespace App\Controller;

use AuditStash\Meta\RequestMetadata;
use Cake\Event\EventManager;
use Cake\Routing\Router;
use Cake\I18n\FrozenDate;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\Exception\UnauthorizedException;

/**
 * Schedules Controller
 *
 * @property \App\Model\Table\SchedulesTable $Schedules
 */
class SchedulesController extends AppController
{
	public function initialize(): void
	{
		parent::initialize();

		$this->loadComponent('Search.Search', [
			'actions' => ['index'],
		]);
	}
	
	public function beforeFilter(\Cake\Event\EventInterface $event)
	{
		parent::beforeFilter($event);
	}

	/*public function viewClasses(): array
    {
        return [JsonView::class];
		return [JsonView::class, XmlView::class];
    }*/
	
	public function json()
    {
		$this->viewBuilder()->setLayout('json');
        $this->set('schedules', $this->paginate());
        $this->viewBuilder()->setOption('serialize', 'schedules');
    }
	
	public function csv()
	{
		$this->response = $this->response->withDownload('schedules.csv');
		$schedules = $this->Schedules->find();
		$_serialize = 'schedules';

		$this->viewBuilder()->setClassName('CsvView.Csv');
		$this->set(compact('schedules', '_serialize'));
	}
	


public function pdfList()
{
    $this->request->allowMethod(['get']);

    // ✅ Setup PDF view
    $this->viewBuilder()->enableAutoLayout(false);
    $this->viewBuilder()->setClassName('CakePdf.Pdf');
    $this->viewBuilder()->setOption('pdfConfig', [
        'orientation' => 'portrait',
        'download' => true,
        'filename' => 'my_schedule.pdf'
    ]);

    // ✅ Get logged-in user
    $user = $this->Authentication->getIdentity();
    $userEmail = $user->email ?? null;

    if (!$userEmail) {
        throw new UnauthorizedException('User email not found.');
    }

    // ✅ Find employee by email
    $employee = $this->Schedules->Employees->find()
        ->where(['email' => $userEmail])
        ->first();

    if (!$employee) {
        throw new UnauthorizedException('Employee record not found for this user.');
    }

    $employeeId = $employee->employee_id;

    // ✅ Get month/year filters
    $month = $this->request->getQuery('month') ?? date('m');
    $year = $this->request->getQuery('year') ?? date('Y');

    $startDate = FrozenDate::create((int)$year, (int)$month, 1);
    $endDate = $startDate->endOfMonth();

    // ✅ Fetch schedule records for the selected month
    $schedules = $this->Schedules->find()
        ->contain(['Shifts'])
        ->where([
            'Schedules.employee_id' => $employeeId,
            'Schedules.work_date >=' => $startDate,
            'Schedules.work_date <=' => $endDate,
        ])
        ->orderAsc('Schedules.work_date')
        ->all();

    // ✅ Pass data to view
    $this->set(compact('schedules', 'startDate', 'endDate', 'employee'));
}

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
   public function index()
{
    $this->set('title', 'My Schedule');

    $user = $this->Authentication->getIdentity();
    $userEmail = $user->email ?? null;
    $userGroupId = $user->user_group_id ?? null;

    // Get employee linked to logged-in user
    $employee = $this->fetchTable('Employees')->find()
        ->where(['email' => $userEmail])
        ->first();

    $employeeId = $employee->employee_id ?? null;

    // ✅ Date filters for paginated list
    $start = $this->request->getQuery('start_date');
    $end = $this->request->getQuery('end_date');

    $query = $this->Schedules->find()
        ->contain(['Employees', 'Shifts'])
        ->order(['Schedules.work_date' => 'ASC']);

    if ($userGroupId != 1 && $employeeId) {
        $query->where(['Schedules.employee_id' => $employeeId]);
    }

    if (!empty($start) && !empty($end)) {
        $query->where(function ($exp, $q) use ($start, $end) {
            return $exp->between('Schedules.work_date', $start, $end);
        });
    }

    $schedules = $this->paginate($query, ['maxLimit' => 10]);

    // ✅ Full calendar events - no date filter
    $calendarQuery = $this->Schedules->find()
        ->contain(['Employees', 'Shifts'])
        ->order(['Schedules.work_date' => 'ASC']);

    if ($userGroupId != 1 && $employeeId) {
        $calendarQuery->where(['Schedules.employee_id' => $employeeId]);
    }

    $calendarEvents = $calendarQuery
        ->all()
        ->map(function ($s) {
            $shiftColors = [1 => '#28a745', 2 => '#ffc107'];
            $mockTime = $s->shift_id == 1 ? '08:00:00' : '16:00:00';

            return [
                'id' => $s->schedule_id,
                'title' => ($s->employee->full_name ?? '-') . ' - ' . ($s->shift->shift_name ?? '-'),
                'start' => $s->work_date->format('Y-m-d') . 'T' . $mockTime,
                'color' => $shiftColors[$s->shift_id] ?? '#6c757d',
                'textColor' => '#fff',
                'allDay' => false,
            ];
        })
        ->toArray();

    // ✅ Public holiday background events
    $publicHolidayDates = $this->fetchTable('PublicHolidays')->find()
        ->select(['holiday_date'])
        ->enableHydration(false)
        ->all()
        ->map(fn($row) => $row['holiday_date']->format('Y-m-d'))
        ->toArray();

    $this->set(compact('schedules', 'calendarEvents', 'publicHolidayDates', 'user'));
}

    /**
     * View method
     *
     * @param string|null $id Schedule id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
		$this->set('title', 'Schedules Details');
        $schedule = $this->Schedules->get($id, contain: ['Employees', 'Shifts']);
        $this->set(compact('schedule'));

        $this->set(compact('schedule'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
		$this->set('title', 'New Schedules');
		EventManager::instance()->on('AuditStash.beforeLog', function ($event, array $logs) {
			foreach ($logs as $log) {
				$log->setMetaInfo($log->getMetaInfo() + ['a_name' => 'Add']);
				$log->setMetaInfo($log->getMetaInfo() + ['c_name' => 'Schedules']);
				$log->setMetaInfo($log->getMetaInfo() + ['ip' => $this->request->clientIp()]);
				$log->setMetaInfo($log->getMetaInfo() + ['url' => Router::url(null, true)]);
				$log->setMetaInfo($log->getMetaInfo() + ['slug' => $this->Authentication->getIdentity('slug')->getIdentifier('slug')]);
			}
		});
        $schedule = $this->Schedules->newEmptyEntity();
        if ($this->request->is('post')) {
            $schedule = $this->Schedules->patchEntity($schedule, $this->request->getData());
            if ($this->Schedules->save($schedule)) {
                $this->Flash->success(__('The schedule has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The schedule could not be saved. Please, try again.'));
        }
        $employees = $this->Schedules->Employees->find('list', ['limit' => 200])->all();
        $shifts = $this->Schedules->Shifts->find('list', ['limit' => 200])->all();
        $this->set(compact('schedule', 'employees', 'shifts'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Schedule id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
		$this->set('title', 'Schedules Edit');
		EventManager::instance()->on('AuditStash.beforeLog', function ($event, array $logs) {
			foreach ($logs as $log) {
				$log->setMetaInfo($log->getMetaInfo() + ['a_name' => 'Edit']);
				$log->setMetaInfo($log->getMetaInfo() + ['c_name' => 'Schedules']);
				$log->setMetaInfo($log->getMetaInfo() + ['ip' => $this->request->clientIp()]);
				$log->setMetaInfo($log->getMetaInfo() + ['url' => Router::url(null, true)]);
				$log->setMetaInfo($log->getMetaInfo() + ['slug' => $this->Authentication->getIdentity('slug')->getIdentifier('slug')]);
			}
		});
        $schedule = $this->Schedules->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $schedule = $this->Schedules->patchEntity($schedule, $this->request->getData());
            if ($this->Schedules->save($schedule)) {
                $this->Flash->success(__('The schedule has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The schedule could not be saved. Please, try again.'));
        }
		$employees = $this->Schedules->Employees->find('list', limit: 200)->all();
		$shifts = $this->Schedules->Shifts->find('list', limit: 200)->all();
        $this->set(compact('schedule', 'employees', 'shifts'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Schedule id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
		EventManager::instance()->on('AuditStash.beforeLog', function ($event, array $logs) {
			foreach ($logs as $log) {
				$log->setMetaInfo($log->getMetaInfo() + ['a_name' => 'Delete']);
				$log->setMetaInfo($log->getMetaInfo() + ['c_name' => 'Schedules']);
				$log->setMetaInfo($log->getMetaInfo() + ['ip' => $this->request->clientIp()]);
				$log->setMetaInfo($log->getMetaInfo() + ['url' => Router::url(null, true)]);
				$log->setMetaInfo($log->getMetaInfo() + ['slug' => $this->Authentication->getIdentity('slug')->getIdentifier('slug')]);
			}
		});
        $this->request->allowMethod(['post', 'delete']);
        $schedule = $this->Schedules->get($id);
        if ($this->Schedules->delete($schedule)) {
            $this->Flash->success(__('The schedule has been deleted.'));
        } else {
            $this->Flash->error(__('The schedule could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
	
	public function archived($id = null)
    {
		$this->set('title', 'Schedules Edit');
		EventManager::instance()->on('AuditStash.beforeLog', function ($event, array $logs) {
			foreach ($logs as $log) {
				$log->setMetaInfo($log->getMetaInfo() + ['a_name' => 'Archived']);
				$log->setMetaInfo($log->getMetaInfo() + ['c_name' => 'Schedules']);
				$log->setMetaInfo($log->getMetaInfo() + ['ip' => $this->request->clientIp()]);
				$log->setMetaInfo($log->getMetaInfo() + ['url' => Router::url(null, true)]);
				$log->setMetaInfo($log->getMetaInfo() + ['slug' => $this->Authentication->getIdentity('slug')->getIdentifier('slug')]);
			}
		});
        $schedule = $this->Schedules->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $schedule = $this->Schedules->patchEntity($schedule, $this->request->getData());
			$schedule->status = 2; //archived
            if ($this->Schedules->save($schedule)) {
                $this->Flash->success(__('The schedule has been archived.'));

				return $this->redirect($this->referer());
            }
            $this->Flash->error(__('The schedule could not be archived. Please, try again.'));
        }
        $this->set(compact('schedule'));
    }
}
