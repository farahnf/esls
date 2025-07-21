<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * PublicHolidays Model
 *
 * @method \App\Model\Entity\PublicHoliday newEmptyEntity()
 * @method \App\Model\Entity\PublicHoliday newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\PublicHoliday> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\PublicHoliday get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\PublicHoliday findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\PublicHoliday patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\PublicHoliday> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\PublicHoliday|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\PublicHoliday saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\PublicHoliday>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\PublicHoliday>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\PublicHoliday>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\PublicHoliday> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\PublicHoliday>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\PublicHoliday>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\PublicHoliday>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\PublicHoliday> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class PublicHolidaysTable extends Table
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

        $this->setTable('public_holidays');
        $this->setDisplayField('holiday_id');
        $this->setPrimaryKey('holiday_id');

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
            ->date('holiday_date')
            ->requirePresence('holiday_date', 'create')
            ->notEmptyDate('holiday_date');

        $validator
            ->scalar('description')
            ->maxLength('description', 255)
            ->allowEmptyString('description');

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
        $rules->add($rules->isUnique(['holiday_date']), ['errorField' => 'holiday_date']);

        return $rules;
    }
}
