<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AppController;

use AuditStash\Meta\RequestMetadata;
use Cake\Event\EventManager;
use Cake\Routing\Router;
use Cake\ORM\RulesChecker;
use Cake\ORM\TableRegistry;
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

    public function updateStatus($id = null)
{
    $this->request->allowMethod(['post', 'put']);
    $schedule = $this->Schedules->get($id);

    $schedule->status = $this->request->getData('status');

 if ($this->Schedules->save($schedule)) {
    $this->Flash->success(__('The schedule has been saved.'));
    return $this->redirect(['action' => 'index']);
} else {
    $errors = $schedule->getErrors();
    if (!empty($errors)) {
        $errorMsg = array_values($errors)[0];
        $this->Flash->error(array_values($errorMsg)[0]); // Show first error
    } else {
        $this->Flash->error(__('The schedule could not be saved. Please, try again.'));
    }
}

    return $this->redirect($this->referer());
}


public function buildRules(RulesChecker $rules): RulesChecker
{
    $rules->add($rules->isUnique(
        ['employee_id', 'work_date'],
        'This employee already has a schedule on this date.'
    ));

    return $rules;
}
	
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
    $this->viewBuilder()->enableAutoLayout(false); 
    $this->viewBuilder()->setClassName('CakePdf.Pdf');

    // Get query params
    $month = $this->request->getQuery('month');
    $year = $this->request->getQuery('year');

    // Default to current if not provided
    $year = $year ?: date('Y');
    $month = $month ?: date('m');

    // Calculate start and end of the selected month
    $startDate = date("{$year}-{$month}-01");
    $endDate = date("Y-m-t", strtotime($startDate)); // Last day of the month

    
    // Query schedules
    $monthlySchedules = $this->Schedules->find('all', [
        'contain' => ['Employees', 'Shifts'],
        'conditions' => [
            'Schedules.work_date >=' => $startDate,
            'Schedules.work_date <=' => $endDate
        ],
        'order' => [
            'Employees.full_name' => 'ASC',
            'Schedules.work_date' => 'ASC'
        ]
    ]);

    // Group by employee
    $groupedSchedules = [];
    foreach ($monthlySchedules as $schedule) {
        $name = $schedule->employee->full_name ?? 'Unknown';
        $groupedSchedules[$name][] = $schedule;
    }

    // Set data to view
    $selectedMonth = $month;
    $selectedYear = $year;

    $this->set(compact('selectedMonth', 'selectedYear', 'groupedSchedules'));

    // PDF config
    $this->viewBuilder()->setOption('pdfConfig', [
        'orientation' => 'portrait',
        'download' => true,
        'filename' => "Schedule_{$month}_{$year}.pdf"
    ]);
}



    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
   public function index()
{
    $this->set('title', 'Schedules List');
    $this->paginate = ['maxLimit' => 10];

    $start = $this->request->getQuery('start_date');
    $end = $this->request->getQuery('end_date');

    $query = $this->Schedules->find()
        ->contain(['Employees', 'Shifts'])
        ->order(['work_date' => 'ASC']);

    if (!empty($start) && !empty($end)) {
        $query->where(function ($exp, $q) use ($start, $end) {
            return $exp->between('work_date', $start, $end);
        });
    }

    $schedules = $this->paginate($query);

    $shiftColors = [1 => '#28a745', 2 => '#ffc107'];

    $calendarEvents = $this->Schedules->find()
        ->contain(['Employees', 'Shifts'])
        ->all()
        ->map(function ($s) use ($shiftColors) {
            $time = $s->shift_id == 1 ? '08:00:00' : '16:00:00';
            return [
                'id' => $s->schedule_id,
                'title' => ($s->employee->full_name ?? 'Unknown') . ' - ' . ($s->shift->shift_name ?? 'Unknown'),
                'start' => $s->work_date->format('Y-m-d') . 'T' . $time,
                'color' => $shiftColors[$s->shift_id] ?? '#6c757d',
                'textColor' => '#fff',
                'allDay' => false,
                'extendedProps' => [
                    'employee_id' => $s->employee_id,
                    'shift_id' => $s->shift_id,
                ]
            ];
        })
        ->toArray();

    // Include public holidays as red background
  
$PublicHolidaysTable = TableRegistry::getTableLocator()->get('PublicHolidays');


$holidaysQuery = $PublicHolidaysTable->find()
    ->select(['holiday_date']) // ✅ correct column
    ->enableHydration(false)
    ->all()
    ->toList();

$publicHolidayDates = array_map(fn($row) => $row['holiday_date']->format('Y-m-d'), $holidaysQuery);



    $holidayEvents = array_map(function ($date) {
        return [
            'title' => 'Public Holiday',
            'start' => date('Y-m-d', strtotime($date)),
            'display' => 'background',
            'color' => '#dc3545',
        ];
    }, $publicHolidayDates);

    $calendarEvents = array_merge($calendarEvents, $holidayEvents);

    $expectedMonths = [];
    for ($i = 11; $i >= 0; $i--) {
        $expectedMonths[] = date('M-Y', strtotime("-$i months"));
    }

    $monthlyQuery = $this->Schedules->find();
    $monthlyQuery->select([
        'count' => $monthlyQuery->func()->count('*'),
        'date' => $monthlyQuery->func()->date_format(['created' => 'identifier', "%b-%Y"]),
        'month' => 'MONTH(created)',
        'year' => 'YEAR(created)'
    ])
    ->where([
        'created >=' => date('Y-m-01', strtotime('-11 months')),
        'created <=' => date('Y-m-t')
    ])
    ->groupBy(['year', 'month'])
    ->orderBy(['year' => 'ASC', 'month' => 'ASC']);

    $results = $monthlyQuery->all()->toArray();

    $totalByMonth = [];
    foreach ($expectedMonths as $expectedMonth) {
        $count = 0;
        foreach ($results as $result) {
            if ($expectedMonth === $result->date) {
                $count = $result->count;
                break;
            }
        }
        $totalByMonth[] = ['month' => $expectedMonth, 'count' => $count];
    }

    $monthArray = array_column($totalByMonth, 'month');
    $countArray = array_column($totalByMonth, 'count');

    $employees = $this->Schedules->Employees->find('list')->all();
    $shifts = $this->Schedules->Shifts->find('list')->all();

    $this->set(compact(
        'schedules',
        'calendarEvents',
        'monthArray',
        'countArray',
        'employees',
        'shifts',
        'start',
        'end', 'publicHolidayDates'
    ));
}


public function editFromCalendar()
{
    $this->request->allowMethod(['post', 'put', 'ajax']);

    $scheduleId = $this->request->getData('schedule_id');
    $employeeId = $this->request->getData('employee_id');
    $shiftId = $this->request->getData('shift_id');

    try {
        $schedule = $this->Schedules->get($scheduleId);

        // Update values
        $schedule = $this->Schedules->patchEntity($schedule, [
            'employee_id' => $employeeId,
            'shift_id' => $shiftId
        ]);

        // Check for duplicate (excluding current record)
        $duplicate = $this->Schedules->find()
            ->where([
                'employee_id' => $employeeId,
                'work_date' => $schedule->work_date,
                'schedule_id !=' => $scheduleId
            ])
            ->count();

        if ($duplicate > 0) {
            return $this->response->withType('application/json')->withStringBody(json_encode([
                'success' => false,
                'message' => 'This employee is already assigned to a shift on that day.'
            ]));
        }

        // Rest day check
        $employee = $this->Schedules->Employees->get($employeeId);
        $scheduledDay = $schedule->work_date->format('l'); // Monday, Tuesday, etc.

        if ($employee->rest_day === $scheduledDay) {
            return $this->response->withType('application/json')->withStringBody(json_encode([
                'success' => false,
                'message' => "This employee's rest day is {$employee->rest_day}. Cannot assign shift."
            ]));
        }

        if ($this->Schedules->save($schedule)) {
            return $this->response->withType('application/json')->withStringBody(json_encode([
                'success' => true,
                'message' => 'Schedule updated successfully.'
            ]));
        } else {
            return $this->response->withType('application/json')->withStringBody(json_encode([
                'success' => false,
                'message' => 'Failed to save schedule.'
            ]));
        }

    } catch (\Exception $e) {
        return $this->response->withType('application/json')->withStringBody(json_encode([
            'success' => false,
            'message' => 'An error occurred: ' . $e->getMessage()
        ]));
    }
}

public function deleteFromCalendar($id = null)
{
    $this->request->allowMethod(['post', 'delete']);
    $schedule = $this->Schedules->get($id);
    if ($this->Schedules->delete($schedule)) {
        $this->Flash->success(__('The schedule has been deleted.'));
    } else {
        $this->Flash->error(__('The schedule could not be deleted.'));
    }
    return $this->redirect(['action' => 'index']);
}

public function updateDate($id = null)
{
    $this->request->allowMethod(['post']);
    $this->autoRender = false;

    $schedule = $this->Schedules->get($id);
    $data = $this->request->input('json_decode', true);

    if (!empty($data['new_date'])) {
        $schedule->work_date = $data['new_date'];
        if ($this->Schedules->save($schedule)) {
            return $this->response->withType('application/json')->withStringBody(json_encode(['success' => true]));
        }
    }

    return $this->response->withStatus(500)->withStringBody(json_encode(['error' => 'Unable to update schedule.']));
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

    // Load public holidays for comparison
   
$PublicHolidaysTable = TableRegistry::getTableLocator()->get('PublicHolidays');


$holidaysQuery = $PublicHolidaysTable->find()
    ->select(['holiday_date']) // ✅ correct column
    ->enableHydration(false)
    ->all()
    ->toList();

$publicHolidayDates = array_map(fn($row) => $row['holiday_date']->format('Y-m-d'), $holidaysQuery);


    if ($this->request->is('post')) {
        $schedule = $this->Schedules->patchEntity($schedule, $this->request->getData());

        $date = $schedule->work_date->format('Y-m-d');

        if (in_array($date, array_map(fn($d) => date('Y-m-d', strtotime($d)), $publicHolidayDates))) {
            $message = 'This date is a public holiday. No schedule allowed.';
            $success = false;
        } else {
            $exists = $this->Schedules->find()
                ->where(['employee_id' => $schedule->employee_id, 'work_date' => $schedule->work_date])
                ->count();

            if ($exists > 0) {
                $message = 'This employee is already assigned on that date.';
                $success = false;
            } else {
                $employee = $this->Schedules->Employees->get($schedule->employee_id);
                $day = $schedule->work_date->format('l');

                if ($employee->rest_day === $day) {
                    $message = 'This is the employee\'s rest day (' . $day . ').';
                    $success = false;
                } elseif ($this->Schedules->save($schedule)) {
                    $message = 'Schedule assigned successfully.';
                    $success = true;
                } else {
                    $message = 'Failed to save schedule.';
                    $success = false;
                }
            }
        }

        if ($this->request->is('ajax')) {
            $this->viewBuilder()->setClassName('Json');
            $this->set(compact('success', 'message'));
            $this->set('_serialize', ['success', 'message']);
            return;
        }

        if ($success) {
            $this->Flash->success(__($message));
            return $this->redirect(['action' => 'index']);
        } else {
            $this->Flash->error(__($message));
        }
    }

    $employees = $this->Schedules->Employees->find('list')->all();
    $shifts = $this->Schedules->Shifts->find('list')->all();

    $shiftColors = [1 => '#28a745', 2 => '#ffc107'];

    $calendarEvents = $this->Schedules->find()
        ->contain(['Employees', 'Shifts'])
        ->all()
        ->map(function ($s) use ($shiftColors) {
            $time = $s->shift_id == 1 ? '08:00:00' : '16:00:00';
            return [
                'id' => $s->schedule_id,
                'title' => ($s->employee->full_name ?? 'Unknown') . ' - ' . ($s->shift->shift_name ?? 'Unknown'),
                'start' => $s->work_date->format('Y-m-d') . 'T' . $time,
                'color' => $shiftColors[$s->shift_id] ?? '#6c757d',
                'textColor' => '#fff',
                'allDay' => false,
                'extendedProps' => [
                    'employee_id' => $s->employee_id,
                    'shift_id' => $s->shift_id
                ]
            ];
        })
        ->toArray();

    // Mark public holidays
    $holidayEvents = array_map(function ($date) {
        return [
            'title' => 'Public Holiday',
            'start' => date('Y-m-d', strtotime($date)),
            'display' => 'background',
            'color' => '#dc3545', // red background
        ];
    }, $publicHolidayDates);

// Load public holidays (as red events)
$holidayEvents = $this->Schedules->PublicHolidays->find()
    ->all()
    ->toList();

$publicHolidayDates = array_map(function ($h) {
    return $h->holiday_date->format('Y-m-d');
}, $holidayEvents);

// Add to calendar as red events
$holidayCalendarEvents = array_map(function ($h) {
    return [
        'title' => 'Public Holiday',
        'start' => $h->holiday_date->format('Y-m-d'),
        'color' => '#dc3545',
        'textColor' => '#fff',
        'allDay' => true
    ];
}, $holidayEvents);

// Merge with shift events
$calendarEvents = array_merge($calendarEvents, $holidayCalendarEvents, $holidayEvents);

    $this->set(compact('schedule', 'employees', 'shifts', 'calendarEvents', 'publicHolidayDates'));


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

    $schedule = $this->Schedules->get($id, ['contain' => []]);

    if ($this->request->is(['patch', 'post', 'put'])) {
        $schedule = $this->Schedules->patchEntity($schedule, $this->request->getData());

        // Check for duplicate (excluding current one)
        $exists = $this->Schedules->find()
            ->where([
                'employee_id' => $schedule->employee_id,
                'work_date' => $schedule->work_date,
                'schedule_id !=' => $schedule->schedule_id
            ])
            ->count();

        if ($exists > 0) {
            $this->Flash->error(__('This employee is already assigned to a shift on that day.'));
        } else {
            $employee = $this->Schedules->Employees->get($schedule->employee_id);
            $scheduledDay = $schedule->work_date->format('l'); // ✅ FIXED HERE

            if ($employee->rest_day === $scheduledDay) {
                $this->Flash->error(__('This employee cannot be scheduled on their rest day (' . $employee->rest_day . ').'));
            } elseif ($this->Schedules->save($schedule)) {
                $this->Flash->success(__('The schedule has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The schedule could not be saved. Please, try again.'));
            }
        }
    }

    $employees = $this->Schedules->Employees->find('list', ['limit' => 200])->all();
    $shifts = $this->Schedules->Shifts->find('list', ['limit' => 200])->all();

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
