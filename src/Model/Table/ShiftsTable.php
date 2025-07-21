<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Shifts Model
 *
 * @method \App\Model\Entity\Shift newEmptyEntity()
 * @method \App\Model\Entity\Shift newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Shift> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Shift get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Shift findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Shift patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Shift> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Shift|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Shift saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Shift>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Shift>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Shift>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Shift> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Shift>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Shift>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Shift>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Shift> deleteManyOrFail(iterable $entities, array $options = [])
 */
class ShiftsTable extends Table
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

        $this->setTable('shifts');
        $this->setDisplayField('shift_name');
        $this->setPrimaryKey('shift_id');
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
            ->scalar('shift_name')
            ->maxLength('shift_name', 50)
            ->requirePresence('shift_name', 'create')
            ->notEmptyString('shift_name');

        $validator
            ->time('start_time')
            ->requirePresence('start_time', 'create')
            ->notEmptyTime('start_time');

        $validator
            ->time('end_time')
            ->requirePresence('end_time', 'create')
            ->notEmptyTime('end_time');

        return $validator;
    }
}
