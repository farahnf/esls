<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AppController;

use AuditStash\Meta\RequestMetadata;
use Cake\Event\EventManager;
use Cake\Routing\Router;
use Cake\I18n\FrozenDate;
use Cake\Http\Exception\UnauthorizedException;

/**
 * Leaves Controller
 *
 * @property \App\Model\Table\LeavesTable $Leaves
 */
class LeavesController extends AppController
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
        $this->set('leaves', $this->paginate());
        $this->viewBuilder()->setOption('serialize', 'leaves');
    }
	
	public function csv()
	{
		$this->response = $this->response->withDownload('leaves.csv');
		$leaves = $this->Leaves->find();
		$_serialize = 'leaves';

		$this->viewBuilder()->setClassName('CsvView.Csv');
		$this->set(compact('leaves', '_serialize'));
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
        'filename' => 'leave_list.pdf',
    ]);

    // ✅ Get logged-in user
    $user = $this->Authentication->getIdentity();
    $userEmail = $user->email ?? null;

    if (!$userEmail) {
        throw new UnauthorizedException('User email not found.');
    }

    // ✅ Get filters (default to current month/year)
    $month = $this->request->getQuery('month') ?? date('m');
    $year = $this->request->getQuery('year') ?? date('Y');
    $employeeId = $this->request->getQuery('employee_id');

    $startDate = FrozenDate::create((int)$year, (int)$month, 1);
    $endDate = $startDate->endOfMonth();

    // ✅ Fetch leave data with related employee & leave type
    $conditions = [
        'Leaves.start_date >=' => $startDate,
        'Leaves.end_date <=' => $endDate,
    ];

    if (!empty($employeeId)) {
        $conditions['Leaves.employee_id'] = $employeeId;
    }

    $leaves = $this->Leaves->find()
        ->contain(['Employees', 'LeaveTypes'])
        ->where($conditions)
        ->orderAsc('Leaves.start_date')
        ->all();

    // ✅ Load employee list for filter dropdown
    $this->Employees = $this->fetchTable('Employees');
    $employees = $this->Employees->find('list', [
        'keyField' => 'employee_id',
        'valueField' => 'full_name'
    ])
    ->orderAsc('full_name')
    ->toArray();

    // ✅ Pass to PDF view
    $this->set(compact('leaves', 'startDate', 'endDate', 'month', 'year', 'employees', 'employeeId'));
    $this->render('pdf_list'); // templates/Admin/Leaves/pdf_list.php
}


public function updateStatus()
{
    $this->request->allowMethod(['post']);

    $leaveId = $this->request->getData('leave_id');
    if (!$leaveId) {
        $this->Flash->error(__('Invalid leave ID.'));
        return $this->redirect($this->referer());
    }

    try {
        $leave = $this->Leaves->get($leaveId);
    } catch (\Exception $e) {
        $this->Flash->error(__('Leave not found.'));
        return $this->redirect($this->referer());
    }

    $newStatus = $this->request->getData('status');
    if (!in_array($newStatus, ['Pending', 'Approved', 'Rejected'])) {
        $this->Flash->error(__('Invalid status value.'));
        return $this->redirect($this->referer());
    }

    $leave->status = $newStatus;
    if ($this->Leaves->save($leave)) {
        $this->Flash->success(__('Leave status updated.'));
    } else {
        $this->Flash->error(__('Failed to update leave status.'));
    }

    return $this->redirect($this->referer());
}


    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
		$this->set('title', 'Leaves List');
		$this->paginate = [
			'maxLimit' => 10,
        ];
        $query = $this->Leaves->find('search', search: $this->request->getQueryParams())
            ->contain(['Employees', 'LeaveTypes']);
			//->where(['title IS NOT' => null])
        $leaves = $this->paginate($query);
		
		//count
		$this->set('total_leaves', $this->Leaves->find()->count());
		$this->set('total_leaves_archived', $this->Leaves->find()->where(['status' => 2])->count());
		$this->set('total_leaves_active', $this->Leaves->find()->where(['status' => 1])->count());
		$this->set('total_leaves_disabled', $this->Leaves->find()->where(['status' => 0])->count());
		
		//Count By Month
		$this->set('january', $this->Leaves->find()->where(['MONTH(created)' => date('1'), 'YEAR(created)' => date('Y')])->count());
		$this->set('february', $this->Leaves->find()->where(['MONTH(created)' => date('2'), 'YEAR(created)' => date('Y')])->count());
		$this->set('march', $this->Leaves->find()->where(['MONTH(created)' => date('3'), 'YEAR(created)' => date('Y')])->count());
		$this->set('april', $this->Leaves->find()->where(['MONTH(created)' => date('4'), 'YEAR(created)' => date('Y')])->count());
		$this->set('may', $this->Leaves->find()->where(['MONTH(created)' => date('5'), 'YEAR(created)' => date('Y')])->count());
		$this->set('jun', $this->Leaves->find()->where(['MONTH(created)' => date('6'), 'YEAR(created)' => date('Y')])->count());
		$this->set('july', $this->Leaves->find()->where(['MONTH(created)' => date('7'), 'YEAR(created)' => date('Y')])->count());
		$this->set('august', $this->Leaves->find()->where(['MONTH(created)' => date('8'), 'YEAR(created)' => date('Y')])->count());
		$this->set('september', $this->Leaves->find()->where(['MONTH(created)' => date('9'), 'YEAR(created)' => date('Y')])->count());
		$this->set('october', $this->Leaves->find()->where(['MONTH(created)' => date('10'), 'YEAR(created)' => date('Y')])->count());
		$this->set('november', $this->Leaves->find()->where(['MONTH(created)' => date('11'), 'YEAR(created)' => date('Y')])->count());
		$this->set('december', $this->Leaves->find()->where(['MONTH(created)' => date('12'), 'YEAR(created)' => date('Y')])->count());

		$query = $this->Leaves->find();

        $expectedMonths = [];
        for ($i = 11; $i >= 0; $i--) {
            $expectedMonths[] = date('M-Y', strtotime("-$i months"));
        }

        $query->select([
            'count' => $query->func()->count('*'),
            'date' => $query->func()->date_format(['created' => 'identifier', "%b-%Y"]),
            'month' => 'MONTH(created)',
            'year' => 'YEAR(created)'
        ])
            ->where([
                'created >=' => date('Y-m-01', strtotime('-11 months')),
                'created <=' => date('Y-m-t')
            ])
            ->groupBy(['year', 'month'])
            ->orderBy(['year' => 'ASC', 'month' => 'ASC']);

        $results = $query->all()->toArray();

        $totalByMonth = [];
        foreach ($expectedMonths as $expectedMonth) {
            $found = false;
            $count = 0;

            foreach ($results as $result) {
                if ($expectedMonth === $result->date) {
                    $found = true;
                    $count = $result->count;
                    break;
                }
            }

            $totalByMonth[] = [
                'month' => $expectedMonth,
                'count' => $count
            ];
        }

        $this->set([
            'results' => $totalByMonth,
            '_serialize' => ['results']
        ]);

        //data as JSON arrays for report chart
        $totalByMonth = json_encode($totalByMonth);
        $dataArray = json_decode($totalByMonth, true);
        $monthArray = [];
        $countArray = [];
        foreach ($dataArray as $data) {
            $monthArray[] = $data['month'];
            $countArray[] = $data['count'];
        }

        $this->set(compact('leaves', 'monthArray', 'countArray'));
    }

    /**
     * View method
     *
     * @param string|null $id Leave id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
		$this->set('title', 'Leaves Details');
        debug($this->request->getData()); // check if leave_id is present
        $leave = $this->Leaves->get($id, contain: ['Employees', 'LeaveTypes']);
        $this->set(compact('leave'));

        $this->set(compact('leave'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
		$this->set('title', 'New Leaves');
		EventManager::instance()->on('AuditStash.beforeLog', function ($event, array $logs) {
			foreach ($logs as $log) {
				$log->setMetaInfo($log->getMetaInfo() + ['a_name' => 'Add']);
				$log->setMetaInfo($log->getMetaInfo() + ['c_name' => 'Leaves']);
				$log->setMetaInfo($log->getMetaInfo() + ['ip' => $this->request->clientIp()]);
				$log->setMetaInfo($log->getMetaInfo() + ['url' => Router::url(null, true)]);
				$log->setMetaInfo($log->getMetaInfo() + ['slug' => $this->Authentication->getIdentity('slug')->getIdentifier('slug')]);
			}
		});
        $leave = $this->Leaves->newEmptyEntity();
        if ($this->request->is('post')) {
            $leave = $this->Leaves->patchEntity($leave, $this->request->getData());
            if ($this->Leaves->save($leave)) {
                $this->Flash->success(__('The leave has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The leave could not be saved. Please, try again.'));
        }
        $employees = $this->Leaves->Employees->find('list', ['limit' => 200])->all();
        $leaveTypes = $this->Leaves->LeaveTypes->find('list', ['limit' => 200])->all();
        $this->set(compact('leave', 'employees', 'leaveTypes'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Leave id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
		$this->set('title', 'Leaves Edit');
		EventManager::instance()->on('AuditStash.beforeLog', function ($event, array $logs) {
			foreach ($logs as $log) {
				$log->setMetaInfo($log->getMetaInfo() + ['a_name' => 'Edit']);
				$log->setMetaInfo($log->getMetaInfo() + ['c_name' => 'Leaves']);
				$log->setMetaInfo($log->getMetaInfo() + ['ip' => $this->request->clientIp()]);
				$log->setMetaInfo($log->getMetaInfo() + ['url' => Router::url(null, true)]);
				$log->setMetaInfo($log->getMetaInfo() + ['slug' => $this->Authentication->getIdentity('slug')->getIdentifier('slug')]);
			}
		});
        $leave = $this->Leaves->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $leave = $this->Leaves->patchEntity($leave, $this->request->getData());
            if ($this->Leaves->save($leave)) {
                $this->Flash->success(__('The leave has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The leave could not be saved. Please, try again.'));
        }
		$employees = $this->Leaves->Employees->find('list', limit: 200)->all();
		$leaveTypes = $this->Leaves->LeaveTypes->find('list', limit: 200)->all();
        $this->set(compact('leave', 'employees', 'leaveTypes'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Leave id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
		EventManager::instance()->on('AuditStash.beforeLog', function ($event, array $logs) {
			foreach ($logs as $log) {
				$log->setMetaInfo($log->getMetaInfo() + ['a_name' => 'Delete']);
				$log->setMetaInfo($log->getMetaInfo() + ['c_name' => 'Leaves']);
				$log->setMetaInfo($log->getMetaInfo() + ['ip' => $this->request->clientIp()]);
				$log->setMetaInfo($log->getMetaInfo() + ['url' => Router::url(null, true)]);
				$log->setMetaInfo($log->getMetaInfo() + ['slug' => $this->Authentication->getIdentity('slug')->getIdentifier('slug')]);
			}
		});
        $this->request->allowMethod(['post', 'delete']);
        $leave = $this->Leaves->get($id);
        if ($this->Leaves->delete($leave)) {
            $this->Flash->success(__('The leave has been deleted.'));
        } else {
            $this->Flash->error(__('The leave could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
	
	public function archived($id = null)
    {
		$this->set('title', 'Leaves Edit');
		EventManager::instance()->on('AuditStash.beforeLog', function ($event, array $logs) {
			foreach ($logs as $log) {
				$log->setMetaInfo($log->getMetaInfo() + ['a_name' => 'Archived']);
				$log->setMetaInfo($log->getMetaInfo() + ['c_name' => 'Leaves']);
				$log->setMetaInfo($log->getMetaInfo() + ['ip' => $this->request->clientIp()]);
				$log->setMetaInfo($log->getMetaInfo() + ['url' => Router::url(null, true)]);
				$log->setMetaInfo($log->getMetaInfo() + ['slug' => $this->Authentication->getIdentity('slug')->getIdentifier('slug')]);
			}
		});
        $leave = $this->Leaves->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $leave = $this->Leaves->patchEntity($leave, $this->request->getData());
			$leave->status = 2; //archived
            if ($this->Leaves->save($leave)) {
                $this->Flash->success(__('The leave has been archived.'));

				return $this->redirect($this->referer());
            }
            $this->Flash->error(__('The leave could not be archived. Please, try again.'));
        }
        $this->set(compact('leave'));
    }
}
