<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * LeaveCategories Model
 *
 * @method \App\Model\Entity\LeaveCategory newEmptyEntity()
 * @method \App\Model\Entity\LeaveCategory newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\LeaveCategory> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\LeaveCategory get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\LeaveCategory findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\LeaveCategory patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\LeaveCategory> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\LeaveCategory|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\LeaveCategory saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\LeaveCategory>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\LeaveCategory>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\LeaveCategory>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\LeaveCategory> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\LeaveCategory>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\LeaveCategory>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\LeaveCategory>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\LeaveCategory> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class LeaveCategoriesTable extends Table
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

        $this->setTable('leave_categories');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
		$this->addBehavior('AuditStash.AuditLog');
		$this->addBehavior('Search.Search');
		$this->searchManager()
			->value('id')
				->add('search', 'Search.Like', [
					//'before' => true,
					//'after' => true,
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
            ->scalar('name')
            ->maxLength('name', 100)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->scalar('description')
            ->allowEmptyString('description');

        $validator
            ->boolean('deducts_annual_leave')
            ->allowEmptyString('deducts_annual_leave');

        $validator
            ->boolean('is_unpaid')
            ->allowEmptyString('is_unpaid');

        return $validator;
    }
}
