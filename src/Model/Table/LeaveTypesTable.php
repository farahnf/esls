<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Leavetypes Model
 *
 * @method \App\Model\Entity\Leavetype newEmptyEntity()
 * @method \App\Model\Entity\Leavetype newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Leavetype> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Leavetype get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Leavetype findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Leavetype patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Leavetype> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Leavetype|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Leavetype saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Leavetype>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Leavetype>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Leavetype>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Leavetype> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Leavetype>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Leavetype>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Leavetype>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Leavetype> deleteManyOrFail(iterable $entities, array $options = [])
 */
class LeavetypesTable extends Table
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

        $this->setTable('leavetypes');
        $this->setDisplayField('leave_type_name');
        $this->setPrimaryKey('leave_type_id');

        $this->belongsTo('LeaveCategories', [
            'foreignKey' => 'leave_category_id',
            'joinType' => 'LEFT',
        ]);

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
                'fields' => ['leave_type_name'],
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
            ->scalar('leave_type_name')
            ->maxLength('leave_type_name', 50)
            ->requirePresence('leave_type_name', 'create')
            ->notEmptyString('leave_type_name');

        return $validator;
    }
}