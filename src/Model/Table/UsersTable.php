<?php
namespace App\Model\Table;

use Cake\Auth\DigestAuthenticate;
use Cake\Event\Event;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Users Model
 *
 * @property |\Cake\ORM\Association\BelongsTo $Schools
 * @property \App\Model\Table\AccountsTable|\Cake\ORM\Association\HasMany $Accounts
 *
 * @method \App\Model\Entity\User get($primaryKey, $options = [])
 * @method \App\Model\Entity\User newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\User[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\User|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\User|bool saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\User patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\User[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\User findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class UsersTable extends Table
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

        $this->setTable('users');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Schools', [
            'foreignKey' => 'school_id',
            'joinType' => 'LEFT'  # LEFT statt INNER - erlaubt User ohne Schule (z.B. Superadmin)
        ]);
        $this->hasMany('Accounts', [
            'foreignKey' => 'user_id'
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
        return $validator
        			->allowEmpty('name')
                    ->notEmpty('username', 'A username is required')
                    ->notEmpty('password', 'A password is required')
                    ->notEmpty('role', 'A role is required')
                    ->add('role', 'inList', [
                        'rule' => ['inList', ['admin', 'user']],
                        'message' => 'Please enter a valid role'
                    ]);
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
        $rules->add($rules->isUnique(['username']));

        return $rules;
    }
    public function beforeSave(Event $event)
        {
            $entity = $event->getData('entity');

            # XSS-Schutz: Name sanitizen
            if (isset($entity->name) && !empty($entity->name)) {
                $entity->name = strip_tags($entity->name);
                $entity->name = html_entity_decode($entity->name, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $entity->name = strip_tags($entity->name);
                $entity->name = trim($entity->name);
            }

            # Verfuegername sanitizen
            if (isset($entity->verfuegername) && !empty($entity->verfuegername)) {
                $entity->verfuegername = strip_tags($entity->verfuegername);
                $entity->verfuegername = html_entity_decode($entity->verfuegername, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $entity->verfuegername = strip_tags($entity->verfuegername);
                $entity->verfuegername = trim($entity->verfuegername);
            }

            # Digest-Hash nur erstellen wenn plain_password gesetzt ist
            if (isset($entity->plain_password) && !empty($entity->plain_password)) {
                $entity->digest_hash = DigestAuthenticate::password(
                    $entity->username,
                    $entity->plain_password,
                    env('SERVER_NAME')
                );
            }
            return true;
        }
}
