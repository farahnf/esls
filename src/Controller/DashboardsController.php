<?php

declare(strict_types=1);

namespace App\Controller;

use Authentication\IdentityInterface;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Controller\Controller;

class DashboardsController extends AppController
	{
    public function initialize(): void
    {
        parent::initialize();
        $this->Leaves = TableRegistry::getTableLocator()->get('Leaves');
    }

    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);
    }

     public function index()
{
    $this->set('title', 'Dashboard');

    $user = $this->Authentication->getIdentity();
    $userEmail = $user->email ?? null;
    $userGroupId = $user->user_group_id ?? null;

    $employee = $this->fetchTable('Employees')->find()
        ->where(['email' => $userEmail])
        ->first();
    $employeeId = $employee->employee_id ?? null;

    $annualLeaveQuota = 20;
    $usedAnnualLeave = 0;
    $remainingAnnualLeave = 0;
    $leaveByCategory = [];
    $pregnancyUnpaidLeaves = 0;

    if ($userGroupId == 1) {
        $leaves = $this->Leaves->find()
            ->contain(['LeaveTypes', 'Employees'])
            ->order(['Leaves.start_date' => 'DESC'])
            ->all();

        $totalLeaves = $this->Leaves->find()->count();
        $activeLeaves = $this->Leaves->find()->where(['status' => 'Approved'])->count();
    } elseif (!empty($employeeId)) {
        $leaves = $this->Leaves->find()
            ->contain(['LeaveTypes.LeaveCategories'])
            ->where(['Leaves.employee_id' => $employeeId])
            ->order(['Leaves.start_date' => 'DESC'])
            ->all();

        $totalLeaves = $leaves->count();

        $activeLeaves = $this->Leaves->find()
            ->where([
                'Leaves.employee_id' => $employeeId,
                'Leaves.status' => 'Approved'
            ])
            ->count();

        foreach ($leaves as $leave) {
            $typeName = $leave->leave_type->leave_type_name ?? 'Unknown';
            $category = $leave->leave_type->leave_category->name ?? 'Unknown';
            $leaveByCategory[$typeName] = ($leaveByCategory[$typeName] ?? 0) + ($leave->total_days ?? 0);

            // ✅ Fix: Only deduct if category is set to deduct annual leave
            if (
                isset($leave->leave_type->leave_category->deducts_annual_leave) &&
                $leave->leave_type->leave_category->deducts_annual_leave &&
                $leave->status === 'Approved'
            ) {
                $usedAnnualLeave += $leave->total_days ?? 0;
            }

            // ✅ Pregnancy unpaid logic
            if (
                strtolower($typeName) === 'pregnancy' &&
                !empty($leave->leave_type->leave_category->is_unpaid) &&
                !empty($employee->pregnancy_start_date)
            ) {
                $pregStart = new \DateTime($employee->pregnancy_start_date);
                $now = new \DateTime();
                $monthsPregnant = $pregStart->diff($now)->m + ($pregStart->diff($now)->y * 12);
                if ($monthsPregnant >= 5) {
                    $pregnancyUnpaidLeaves += $leave->total_days ?? 0;
                }
            }
        }

        $remainingAnnualLeave = $annualLeaveQuota - $usedAnnualLeave;
    } else {
        $leaves = [];
        $totalLeaves = 0;
        $activeLeaves = 0;
    }

    // User Logs
    $UserLogs = $this->fetchTable('UserLogs');
    $userLogs = $UserLogs->find()
        ->where(['user_id' => $user->id])
        ->order(['created' => 'DESC'])
        ->limit(5)
        ->all();

    // User Stats
    $users = $this->fetchTable('Users');
    $total_user = $users->find()->count();
    $active_user = $users->find()->where(['status' => 1])->count();
    $user_percent = $total_user > 0 ? ($active_user * 100 / $total_user) : 0;

    // Contacts
    $contacts = $this->fetchTable('Contacts');
    $total_contact = $contacts->find()->count();
    $pending_contact = $contacts->find()->where(['status' => 0])->count();
    $pending_contact_percent = $total_contact > 0 ? ($pending_contact * 100 / $total_contact) : 0;

    // Audit Logs
    $auditLogs = $this->fetchTable('AuditLogs');
    $total_auditlog = $auditLogs->find()->count();

    // Todos
    $todos = $this->fetchTable('Todos');
    $total_todo = $todos->find()->count();
    $pending_todo = $todos->find()->where(['status' => 'Pending'])->count();
    $pending_todo_percent = $total_todo > 0 ? ($pending_todo * 100 / $total_todo) : 0;
    $todo_list = $todos->find()
        ->where(['status IN' => ['Pending', 'In Progress']])
        ->limit(5)
        ->orderBy(['created' => 'DESC']);

    // FAQs
    $faqs = $this->fetchTable('Faqs');
    $total_faq = $faqs->find()->count();
    $pending_faq = $faqs->find()->where(['status' => 1])->count();
    $pending_faq_percent = $total_faq > 0 ? ($pending_faq * 100 / $total_faq) : 0;

    // Heatmap
    $activityQuery = $UserLogs->find();
    $activityQuery->select([
        'count' => $activityQuery->func()->count('*'),
        'date' => $activityQuery->func()->date_format(['created' => 'identifier', "%Y-%m-%d"])
    ])->groupBy(['date']);
    $formattedResults = array_map(fn($r) => ['date' => $r->date, 'count' => $r->count], $activityQuery->all()->toArray());

    $this->set(compact(
        'leaves',
        'totalLeaves',
        'activeLeaves',
        'usedAnnualLeave',
        'remainingAnnualLeave',
        'leaveByCategory',
        'pregnancyUnpaidLeaves',
        'userLogs',
        'total_user',
        'active_user',
        'user_percent',
        'total_contact',
        'pending_contact',
        'pending_contact_percent',
        'total_auditlog',
        'total_todo',
        'pending_todo',
        'pending_todo_percent',
        'todo_list',
        'total_faq',
        'pending_faq',
        'pending_faq_percent',
        'formattedResults'
    ));
}
    }