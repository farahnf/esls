<?php
declare(strict_types=1);

namespace App\Controller;

use AuditStash\Meta\RequestMetadata;
use Cake\Event\EventManager;
use Cake\Routing\Router;

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
		$query = $this->Leaves->find()->contain(['Employees', 'LeaveTypes'])->limit(10);
$leaves = $this->paginate($query);
		$this->viewBuilder()->setClassName('CakePdf.Pdf');
		$this->viewBuilder()->setOption(
			'pdfConfig',
			[
				'orientation' => 'portrait',
				'download' => true, 
				'filename' => 'leaves_List.pdf' 
			]
		);
		$this->set(compact('leaves'));
	}
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
public function index()
{
    $this->set('title', 'Leaves');

    $user = $this->Authentication->getIdentity();
    $userEmail = $user->email ?? null;
    $userGroupId = $user->user_group_id ?? null;

    $employee = $this->fetchTable('Employees')->find()
        ->where(['email' => $userEmail])
        ->first();

    $employeeId = $employee->employee_id ?? null;

    if ($userGroupId == 1) {
        // Admin sees all
        $query = $this->Leaves->find()
            ->contain(['LeaveTypes', 'Employees'])
            ->order(['Leaves.start_date' => 'DESC']);
    } elseif (!empty($employeeId)) {
        // Regular user sees own
        $query = $this->Leaves->find()
            ->contain(['LeaveTypes', 'Employees']) // 👈 needed for employee name
            ->where(['Leaves.employee_id' => $employeeId])
            ->order(['Leaves.start_date' => 'DESC']);
    } else {
        $query = $this->Leaves->find()
            ->where(['Leaves.id' => 0]); // return nothing
    }

    $leaves = $this->paginate($query);

    $this->set(compact('leaves'));
}




public function beforeRender(\Cake\Event\EventInterface $event)
{
    parent::beforeRender($event);

    // Count of pending leaves (if you want to show globally or only for current user, adjust accordingly)
    $pendingLeavesCount = $this->Leaves->find()->where(['status' => 'Pending'])->count();
    $this->set(compact('pendingLeavesCount'));
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
    $this->set('title', 'Leave Details');

    // Get the logged-in employee's ID
    $employeeId = $this->Authentication->getIdentity()->id;

    // Fetch the leave record
    $leave = $this->Leaves->get($id, ['contain' => ['Employees', 'LeaveTypes']]);

    // Ensure the employee can only view their own leave
    if ($leave->employee_id !== $employeeId) {
        $this->Flash->error(__('You are not authorized to view this leave.'));
        return $this->redirect(['action' => 'index']);
    }

    $this->set(compact('leave'));
}

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
public function add()
{
    $this->set('title', 'Apply Leave');

    $user = $this->Authentication->getIdentity();
    $userEmail = $user->email;

    $employee = $this->Leaves->Employees->find()
        ->where(['email' => $userEmail])
        ->first();

    if (!$employee) {
        $this->Flash->error(__('You are not registered as an employee.'));
        return $this->redirect(['action' => 'index']);
    }

    EventManager::instance()->on('AuditStash.beforeLog', function ($event, array $logs) {
        foreach ($logs as $log) {
            $log->setMetaInfo($log->getMetaInfo() + [
                'a_name' => 'Add',
                'c_name' => 'Leaves',
                'ip' => $this->request->clientIp(),
                'url' => Router::url(null, true),
                'slug' => $this->Authentication->getIdentity()->get('slug')
            ]);
        }
    });

    $leave = $this->Leaves->newEmptyEntity();

    if ($this->request->is('post')) {
        $data = $this->request->getData();

        // ✅ Handle file upload (if any)
        $uploadedFile = $data['attachment'] ?? null;
        if ($uploadedFile instanceof \Laminas\Diactoros\UploadedFile && $uploadedFile->getError() === UPLOAD_ERR_OK) {
            $filename = time() . '_' . $uploadedFile->getClientFilename();
            $uploadPath = WWW_ROOT . 'uploads' . DS;
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            $uploadedFile->moveTo($uploadPath . $filename);
            $data['attachment'] = $filename;
        } else {
            unset($data['attachment']); // Prevent type error
        }

        // ✅ Fill required fields
        $data['employee_id'] = $employee->employee_id;
        $data['applied_on'] = $data['applied_on'] ?? date('Y-m-d H:i:s');

        // ✅ Auto-fill category info
        if (!empty($data['leave_type_id'])) {
            $leaveType = $this->Leaves->LeaveTypes->get($data['leave_type_id'], ['contain' => ['LeaveCategories']]);
            $data['leave_category_id'] = $leaveType->leave_category_id ?? null;
            $data['deduct_from_annual'] = $leaveType->leave_category->deducts_annual_leave ?? 0;
            $data['is_unpaid'] = $leaveType->leave_category->is_unpaid ?? 0;
        }

        // ✅ Temporary total_days (actual is recalculated in beforeSave)
        if (!empty($data['start_date']) && !empty($data['end_date'])) {
            $start = new \DateTime($data['start_date']);
            $end = new \DateTime($data['end_date']);
            $data['total_days'] = $start->diff($end)->days + 1;
        }

        $leave = $this->Leaves->patchEntity($leave, $data);

        if ($this->Leaves->save($leave)) {
            $this->Flash->success(__('The leave has been saved.'));
            return $this->redirect(['action' => 'index']);
        }

        $this->Flash->error(__('The leave could not be saved. Please, try again.'));
        Log::write('error', 'Leave save failed: ' . json_encode($leave->getErrors()));
    }

    $leaveTypes = $this->Leaves->LeaveTypes->find('list')->all();

    // ✅ Fetch all leaves for stats (regardless of status)
    $allLeaves = $this->Leaves->find()
        ->contain(['LeaveTypes.LeaveCategories'])
        ->where([
            'Leaves.employee_id' => $employee->employee_id
        ])
        ->all();

    $annualLeaveQuota = 20;
    $usedAnnualLeave = 0;
    $leaveByCategory = [];

  foreach ($allLeaves as $record) {
    $typeName = $record->leave_type->leave_type_name ?? 'Unknown';
    $category = $record->leave_type->leave_category->name ?? 'Unknown';
    $days = $record->total_days ?? 0;

    // ✅ Always add to usage summary
    $leaveByCategory[$typeName] = ($leaveByCategory[$typeName] ?? 0) + $days;

    // ✅ Only deduct from annual if approved and marked to deduct
    if (
        $record->status === 'Approved' &&
        !empty($record->leave_type->leave_category->deducts_annual_leave)
    ) {
        $usedAnnualLeave += $days;
    }
}


    $remainingAnnualLeave = $annualLeaveQuota - $usedAnnualLeave;

    $this->set(compact('leave', 'leaveTypes', 'leaveByCategory', 'remainingAnnualLeave'));
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
    $leave = $this->Leaves->get($id);

    if ($this->request->is(['patch', 'post', 'put'])) {
        $data = $this->request->getData();

        // ✅ Handle updated attachment if any
        if (!empty($data['attachment']) && $data['attachment'] instanceof \Laminas\Diactoros\UploadedFile && $data['attachment']->getError() === UPLOAD_ERR_OK) {
            $file = $data['attachment'];
            $filename = time() . '_' . $file->getClientFilename();
            $uploadPath = WWW_ROOT . 'uploads' . DS;
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            $file->moveTo($uploadPath . $filename);
            $data['attachment'] = $filename;
        } else {
            unset($data['attachment']); // prevent upload object error
        }

        $leave = $this->Leaves->patchEntity($leave, $data);

        if ($leave->status === 'Approved' && $leave->leave_type_id == 1) { // Annual Leave
            $start = new \DateTime($leave->start_date);
            $end = new \DateTime($leave->end_date);
            $interval = $start->diff($end);
            $leave->total_days = $interval->days + 1;
            $leave->deduct_from_annual = $leave->total_days;
        }

        if ($this->Leaves->save($leave)) {
            $this->Flash->success(__('The leave has been updated.'));
            return $this->redirect(['action' => 'index']);
        }
        $this->Flash->error(__('The leave could not be updated. Please, try again.'));
    }

    // Leave balance section
    $employeeId = $leave->employee_id;
    $this->loadModel('Employees');
    $employee = $this->Employees->get($employeeId);

    $annualLeaveTypeId = 1; // Annual Leave
    $usedAnnualLeaveQuery = $this->Leaves->find()
        ->where([
            'employee_id' => $employeeId,
            'leave_type_id' => $annualLeaveTypeId,
            'status' => 'Approved'
        ])
        ->select(['total' => $this->Leaves->find()->func()->sum('deduct_from_annual')])
        ->first();

    $usedAnnualLeave = $usedAnnualLeaveQuery->total ?? 0;
    $annualEntitlement = 20;
    $remainingAnnualLeave = $annualEntitlement - $usedAnnualLeave;

    $this->set(compact('leave', 'employee', 'usedAnnualLeave', 'remainingAnnualLeave', 'annualEntitlement'));
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
