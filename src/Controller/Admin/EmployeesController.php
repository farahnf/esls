<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AppController;

use AuditStash\Meta\RequestMetadata;
use Cake\Event\EventManager;
use Cake\Routing\Router;

/**
 * Employees Controller
 *
 * @property \App\Model\Table\EmployeesTable $Employees
 */
class EmployeesController extends AppController
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
        $this->set('employees', $this->paginate());
        $this->viewBuilder()->setOption('serialize', 'employees');
    }
	
	public function csv()
	{
		$this->response = $this->response->withDownload('employees.csv');
		$employees = $this->Employees->find();
		$_serialize = 'employees';

		$this->viewBuilder()->setClassName('CsvView.Csv');
		$this->set(compact('employees', '_serialize'));
	}
	
	public function pdfList()
	{
		$this->viewBuilder()->enableAutoLayout(false); 
		$employees = $this->paginate($this->Employees);
		$this->viewBuilder()->setClassName('CakePdf.Pdf');
		$this->viewBuilder()->setOption(
			'pdfConfig',
			[
				'orientation' => 'portrait',
				'download' => true, 
				'filename' => 'employees_List.pdf' 
			]
		);
		$this->set(compact('employees'));
	}
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
		$this->set('title', 'Employees List');
		$this->paginate = [
			'maxLimit' => 10,
        ];
        $query = $this->Employees->find('search', search: $this->request->getQueryParams())
    ->select([
        'employee_id',
        'full_name',
        'email',
        'phone',
        'hire_date',
        'rest_day', // ✅ Add this
    ]);
        $employees = $this->paginate($query);
		
		//count
		$this->set('total_employees', $this->Employees->find()->count());
		$this->set('total_employees_archived', $this->Employees->find()->where(['status' => 2])->count());
		$this->set('total_employees_active', $this->Employees->find()->where(['status' => 1])->count());
		$this->set('total_employees_disabled', $this->Employees->find()->where(['status' => 0])->count());
		
		//Count By Month
		$this->set('january', $this->Employees->find()->where(['MONTH(created)' => date('1'), 'YEAR(created)' => date('Y')])->count());
		$this->set('february', $this->Employees->find()->where(['MONTH(created)' => date('2'), 'YEAR(created)' => date('Y')])->count());
		$this->set('march', $this->Employees->find()->where(['MONTH(created)' => date('3'), 'YEAR(created)' => date('Y')])->count());
		$this->set('april', $this->Employees->find()->where(['MONTH(created)' => date('4'), 'YEAR(created)' => date('Y')])->count());
		$this->set('may', $this->Employees->find()->where(['MONTH(created)' => date('5'), 'YEAR(created)' => date('Y')])->count());
		$this->set('jun', $this->Employees->find()->where(['MONTH(created)' => date('6'), 'YEAR(created)' => date('Y')])->count());
		$this->set('july', $this->Employees->find()->where(['MONTH(created)' => date('7'), 'YEAR(created)' => date('Y')])->count());
		$this->set('august', $this->Employees->find()->where(['MONTH(created)' => date('8'), 'YEAR(created)' => date('Y')])->count());
		$this->set('september', $this->Employees->find()->where(['MONTH(created)' => date('9'), 'YEAR(created)' => date('Y')])->count());
		$this->set('october', $this->Employees->find()->where(['MONTH(created)' => date('10'), 'YEAR(created)' => date('Y')])->count());
		$this->set('november', $this->Employees->find()->where(['MONTH(created)' => date('11'), 'YEAR(created)' => date('Y')])->count());
		$this->set('december', $this->Employees->find()->where(['MONTH(created)' => date('12'), 'YEAR(created)' => date('Y')])->count());

		$query = $this->Employees->find();

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

        $this->set(compact('employees', 'monthArray', 'countArray'));
        
    }

    /**
     * View method
     *
     * @param string|null $id Employee id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
		$this->set('title', 'Employees Details');
        $employee = $this->Employees->get($id, contain: []);
        $this->set(compact('employee'));

        $this->set(compact('employee'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
{
    $this->set('title', 'New Employees');

    EventManager::instance()->on('AuditStash.beforeLog', function ($event, array $logs) {
        foreach ($logs as $log) {
            $log->setMetaInfo($log->getMetaInfo() + ['a_name' => 'Add']);
            $log->setMetaInfo($log->getMetaInfo() + ['c_name' => 'Employees']);
            $log->setMetaInfo($log->getMetaInfo() + ['ip' => $this->request->clientIp()]);
            $log->setMetaInfo($log->getMetaInfo() + ['url' => Router::url(null, true)]);
            $log->setMetaInfo($log->getMetaInfo() + ['slug' => $this->Authentication->getIdentity('slug')->getIdentifier('slug')]);
        }
    });

    $employee = $this->Employees->newEmptyEntity();

    if ($this->request->is('post')) {
        $employee = $this->Employees->patchEntity($employee, $this->request->getData());

        if ($this->Employees->save($employee)) {
            $this->Flash->success(__('The employee has been saved.'));
            return $this->redirect(['action' => 'index']);
        }

        $this->Flash->error(__('The employee could not be saved. Please, try again.'));
    }

    // Days of the week for dropdown
    $restDays = [
        'Sunday' => 'Sunday',
        'Monday' => 'Monday',
        'Tuesday' => 'Tuesday',
        'Wednesday' => 'Wednesday',
        'Thursday' => 'Thursday',
        'Friday' => 'Friday',
        'Saturday' => 'Saturday'
    ];

    $this->set(compact('employee', 'restDays'));
}


    /**
     * Edit method
     *
     * @param string|null $id Employee id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
{
    $this->set('title', 'Employees Edit');

    EventManager::instance()->on('AuditStash.beforeLog', function ($event, array $logs) {
        foreach ($logs as $log) {
            $log->setMetaInfo($log->getMetaInfo() + ['a_name' => 'Edit']);
            $log->setMetaInfo($log->getMetaInfo() + ['c_name' => 'Employees']);
            $log->setMetaInfo($log->getMetaInfo() + ['ip' => $this->request->clientIp()]);
            $log->setMetaInfo($log->getMetaInfo() + ['url' => Router::url(null, true)]);
            $log->setMetaInfo($log->getMetaInfo() + ['slug' => $this->Authentication->getIdentity('slug')->getIdentifier('slug')]);
        }
    });

    $employee = $this->Employees->get($id, ['contain' => []]);

    if ($this->request->is(['patch', 'post', 'put'])) {
        $employee = $this->Employees->patchEntity($employee, $this->request->getData());

        if ($this->Employees->save($employee)) {
            $this->Flash->success(__('The employee has been saved.'));
            return $this->redirect(['action' => 'index']);
        }

        $this->Flash->error(__('The employee could not be saved. Please, try again.'));
    }

    $restDays = [
        'Sunday' => 'Sunday',
        'Monday' => 'Monday',
        'Tuesday' => 'Tuesday',
        'Wednesday' => 'Wednesday',
        'Thursday' => 'Thursday',
        'Friday' => 'Friday',
        'Saturday' => 'Saturday'
    ];

    $this->set(compact('employee', 'restDays'));
}

    /**
     * Delete method
     *
     * @param string|null $id Employee id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
		EventManager::instance()->on('AuditStash.beforeLog', function ($event, array $logs) {
			foreach ($logs as $log) {
				$log->setMetaInfo($log->getMetaInfo() + ['a_name' => 'Delete']);
				$log->setMetaInfo($log->getMetaInfo() + ['c_name' => 'Employees']);
				$log->setMetaInfo($log->getMetaInfo() + ['ip' => $this->request->clientIp()]);
				$log->setMetaInfo($log->getMetaInfo() + ['url' => Router::url(null, true)]);
				$log->setMetaInfo($log->getMetaInfo() + ['slug' => $this->Authentication->getIdentity('slug')->getIdentifier('slug')]);
			}
		});
        $this->request->allowMethod(['post', 'delete']);
        $employee = $this->Employees->get($id);
        if ($this->Employees->delete($employee)) {
            $this->Flash->success(__('The employee has been deleted.'));
        } else {
            $this->Flash->error(__('The employee could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
	
	public function archived($id = null)
    {
		$this->set('title', 'Employees Edit');
		EventManager::instance()->on('AuditStash.beforeLog', function ($event, array $logs) {
			foreach ($logs as $log) {
				$log->setMetaInfo($log->getMetaInfo() + ['a_name' => 'Archived']);
				$log->setMetaInfo($log->getMetaInfo() + ['c_name' => 'Employees']);
				$log->setMetaInfo($log->getMetaInfo() + ['ip' => $this->request->clientIp()]);
				$log->setMetaInfo($log->getMetaInfo() + ['url' => Router::url(null, true)]);
				$log->setMetaInfo($log->getMetaInfo() + ['slug' => $this->Authentication->getIdentity('slug')->getIdentifier('slug')]);
			}
		});
        $employee = $this->Employees->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $employee = $this->Employees->patchEntity($employee, $this->request->getData());
			$employee->status = 2; //archived
            if ($this->Employees->save($employee)) {
                $this->Flash->success(__('The employee has been archived.'));

				return $this->redirect($this->referer());
            }
            $this->Flash->error(__('The employee could not be archived. Please, try again.'));
        }
        $this->set(compact('employee'));
    }
}
