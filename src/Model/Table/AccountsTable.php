<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Event\Event;

/**
 * Accounts Model
 *
 * @property \App\Model\Table\UsersTable|\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\TransactionsTable|\Cake\ORM\Association\HasMany $Transactions
 *
 * @method \App\Model\Entity\Account get($primaryKey, $options = [])
 * @method \App\Model\Entity\Account newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Account[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Account|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Account|bool saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Account patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Account[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Account findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class AccountsTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('accounts');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('Transactions', [
            'foreignKey' => 'account_id'
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->integer('user_id')
            ->notEmpty('user_id');

        $validator
            ->scalar('name')
            ->maxLength('name', 255)
            ->notEmpty('name');

        $validator
            ->scalar('iban')
            ->maxLength('iban', 255)
            ->notEmpty('iban');

        $validator
            ->scalar('bic')
            ->maxLength('bic', 255)
            ->notEmpty('bic');

        $validator
            ->numeric('maxlimit')
            ->allowEmpty('maxlimit');

        $validator
            ->numeric('balance')
            ->allowEmpty('balance');


        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['user_id'], 'Users'));
        $rules->add($rules->isUnique(['school_id']));
        return $rules;
    }

    /**
     * Sanitize input vor dem Speichern (XSS-Schutz)
     */
    public function beforeSave(Event $event, $entity, $options)
    {
        if (isset($entity->name) && !empty($entity->name)) {
            $entity->name = strip_tags($entity->name);
            $entity->name = html_entity_decode($entity->name, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $entity->name = strip_tags($entity->name);
            $entity->name = trim($entity->name);
        }
        return true;
    }
}
