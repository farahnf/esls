<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Event\EventInterface;
use Cake\Datasource\EntityInterface;
use Cake\Http\Exception\BadRequestException;
use Cake\I18n\FrozenDate;

/**
 * Leaves Model
 *
 * @property \App\Model\Table\EmployeesTable&\Cake\ORM\Association\BelongsTo $Employees
 * @property \App\Model\Table\LeavetypesTable&\Cake\ORM\Association\BelongsTo $LeaveTypes
 * @property \App\Model\Table\LeaveCategoriesTable&\Cake\ORM\Association\BelongsTo $LeaveCategories
 *
 * @method \App\Model\Entity\Leave newEmptyEntity()
 * @method \App\Model\Entity\Leave newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Leave> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Leave get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Leave findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Leave patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Leave> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Leave|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Leave saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Leave>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Leave>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Leave>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Leave> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Leave>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Leave>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Leave>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Leave> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class LeavesTable extends Table
{
    /**
     * Initialize method
     *
     * @param array<string, mixed> $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
{
    parent::initialize($config);

    $this->setTable('leaves');
    $this->setDisplayField('leave_id');
    $this->setPrimaryKey('leave_id');

    $this->belongsTo('Employees', [
        'foreignKey' => 'employee_id',
        'joinType' => 'INNER',
    ]);

    $this->belongsTo('LeaveTypes', [
        'foreignKey' => 'leave_type_id',
        'joinType' => 'LEFT',
    ]);

    $this->belongsTo('LeaveCategories', [
        'foreignKey' => 'leave_category_id',
        'joinType' => 'LEFT',
    ]);

    $this->addBehavior('Timestamp');
    $this->addBehavior('AuditStash.AuditLog');
    $this->addBehavior('Search.Search');

    $this->searchManager()
        ->value('id')
        ->add('search', 'Search.Like', [
            'fieldMode' => 'OR',
            'multiValue' => true,
            'multiValueSeparator' => '|',
            'comparison' => 'LIKE',
            'wildcardAny' => '*',
            'wildcardOne' => '?',
            'fields' => ['id'],
        ]);
}

    

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
       public function validationDefault(Validator $validator): Validator
    {

          $validator
        ->integer('leave_id')
        ->allowEmptyString('leave_id', null, 'create');

       $validator
    ->scalar('status')
    ->maxLength('status', 20)
    ->notEmptyString('status', 'Status is required');

        $validator
            ->integer('employee_id')
            ->requirePresence('employee_id')
            ->notEmptyString('employee_id');

        $validator
            ->integer('leave_type_id')
            ->requirePresence('leave_type_id')
            ->notEmptyString('leave_type_id');

        $validator
            ->integer('leave_category_id')
            ->allowEmptyString('leave_category_id'); // <-- Allow empty, we fill later

        $validator
            ->date('start_date')
            ->requirePresence('start_date')
            ->notEmptyDate('start_date');

        $validator
            ->date('end_date')
            ->requirePresence('end_date')
            ->notEmptyDate('end_date');

        $validator
            ->scalar('reason')
            ->allowEmptyString('reason');

  
        $validator
    ->add('status', 'validStatus', [
        'rule' => ['inList', ['Pending', 'Approved', 'Rejected']],
        'message' => 'Please enter a valid status'
    ]);

        $validator
            ->dateTime('applied_on')
            ->allowEmptyDateTime('applied_on');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
     public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['employee_id'], 'Employees'), ['errorField' => 'employee_id']);
        $rules->add($rules->existsIn(['leave_type_id'], 'LeaveTypes'), ['errorField' => 'leave_type_id']);
        $rules->add($rules->existsIn(['leave_category_id'], 'LeaveCategories'), ['errorField' => 'leave_category_id']);

        return $rules;
    }
    
    /**
     * Custom finder to get leaves by employee
     *
     * @param \Cake\ORM\Query\SelectQuery $query The query instance
     * @param array $options The options array, expecting 'employee_id'
     * @return \Cake\ORM\Query\SelectQuery The modified query object
     */
    
  public function findByEmployee(SelectQuery $query, array $options)
    {
        $employeeId = $options['employee_id'] ?? null;

        if ($employeeId) {
            $query->where(['Leaves.employee_id' => $employeeId]);
        }

        return $query->contain(['Employees', 'LeaveTypes', 'LeaveCategories']);
    }

    public function beforeSave(EventInterface $event, EntityInterface $entity, \ArrayObject $options)
    {
        // ✅ Get leave type and its category
        $leaveType = $this->LeaveTypes->get($entity->leave_type_id, ['contain' => ['LeaveCategories']]);
        $category = $leaveType->leave_category ?? null;

        // ✅ Auto-fill leave_category_id if empty
        if (!$entity->leave_category_id && $leaveType->leave_category_id) {
            $entity->leave_category_id = $leaveType->leave_category_id;
        }

        // ✅ Check date presence
        if (!$entity->has('start_date') || !$entity->has('end_date')) {
            return true;
        }

        $start = new \DateTime($entity->start_date->format('Y-m-d'));
        $end = new \DateTime($entity->end_date->format('Y-m-d'));

        // ✅ Count total days excluding rest day
        $interval = new \DatePeriod($start, new \DateInterval('P1D'), (clone $end)->modify('+1 day'));
        $totalDays = 0;

        $employee = $this->Employees->get($entity->employee_id);
        $restDay = $employee->rest_day ?? null;

        foreach ($interval as $date) {
            if (!$restDay || $date->format('l') !== $restDay) {
                $totalDays++;
            }
        }

        $entity->total_days = $totalDays;

        // ✅ Deduct from annual leave
        $entity->deduct_from_annual = $category && $category->deducts_annual_leave;

      if ($entity->deduct_from_annual) {
    $usedLeaveQuery = $this->find()
        ->select(['sum' => $this->find()->func()->sum('total_days')])
        ->where([
            'employee_id' => $entity->employee_id,
            'deduct_from_annual' => true,
            'YEAR(start_date)' => (new \DateTime())->format('Y'),
            'status !=' => 'rejected'
        ])
        ->first();

    $usedLeave = $usedLeaveQuery->get('sum') ?? 0;
    $remaining = 20 - $usedLeave;

    if ($entity->total_days > $remaining) {
        throw new BadRequestException("Annual leave exceeded. Remaining: $remaining day(s).");
    }
}


        // ✅ Unpaid leave handling
        if ($category && $category->is_unpaid) {
            $entity->is_unpaid = true;
        }

        // ✅ Pregnancy logic
        if (strtolower($leaveType->leave_type_name ?? '') === 'pregnancy') {
            if (!empty($employee->pregnancy_start_date)) {
                $pregnancyStart = new \DateTime($employee->pregnancy_start_date->format('Y-m-d'));
                $now = new \DateTime();
                $diff = $pregnancyStart->diff($now);
                $monthsPregnant = $diff->m + ($diff->y * 12);

                if ($monthsPregnant >= 5) {
                    $entity->is_unpaid = true;
                }
            }
        }

        return true;
    }
}