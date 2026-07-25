<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * LibraryGames Model
 *
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 *
 * @method \App\Model\Entity\LibraryGame newEmptyEntity()
 * @method \App\Model\Entity\LibraryGame newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\LibraryGame> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\LibraryGame get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\LibraryGame findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\LibraryGame patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\LibraryGame> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\LibraryGame|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\LibraryGame saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\LibraryGame>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\LibraryGame>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\LibraryGame>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\LibraryGame> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\LibraryGame>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\LibraryGame>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\LibraryGame>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\LibraryGame> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class LibraryGamesTable extends Table
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

        $this->setTable('library_games');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
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
            ->integer('user_id')
            ->notEmptyString('user_id');

        $validator
            ->integer('rawg_id')
            ->requirePresence('rawg_id', 'create')
            ->notEmptyString('rawg_id');

        $validator
            ->scalar('title')
            ->maxLength('title', 255)
            ->requirePresence('title', 'create')
            ->notEmptyString('title');

        $validator
            ->scalar('cover_url')
            ->maxLength('cover_url', 255)
            ->allowEmptyString('cover_url');

        $validator
            ->scalar('genres')
            ->maxLength('genres', 255)
            ->allowEmptyString('genres');

        $validator
            ->decimal('rating')
            ->allowEmptyString('rating');

        $validator
            ->scalar('status')
            ->maxLength('status', 20)
            ->notEmptyString('status')
            ->inList('status', ['playing', 'completed', 'backlog', 'wishlist'], 'Invalid status.');

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
        $rules->add($rules->isUnique(['user_id', 'rawg_id']), ['errorField' => 'user_id', 'message' => __('This combination of user_id and rawg_id already exists')]);
        $rules->add($rules->existsIn(['user_id'], 'Users'), ['errorField' => 'user_id']);

        return $rules;
    }
}
