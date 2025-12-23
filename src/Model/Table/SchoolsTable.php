<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Event\Event;

/**
 * Schools Model
 *
 * @property \App\Model\Table\UsersTable|\Cake\ORM\Association\HasMany $Users
 *
 * @method \App\Model\Entity\School get($primaryKey, $options = [])
 * @method \App\Model\Entity\School newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\School[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\School|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\School|bool saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\School patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\School[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\School findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class SchoolsTable extends Table
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

        $this->setTable('schools');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('Users', [
            'foreignKey' => 'school_id'
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
            ->scalar('name')
            ->maxLength('name', 255)
            ->requirePresence('name', 'create')
            ->notEmpty('name');

        $validator
            ->scalar('kurzname')
            ->maxLength('kurzname', 20)
            ->requirePresence('kurzname', 'create')
            ->notEmpty('kurzname');

        $validator
            ->scalar('ibanprefix')
            ->maxLength('ibanprefix', 4)
            ->requirePresence('ibanprefix', 'create')
            ->notEmpty('ibanprefix');

        $validator
            ->scalar('bic')
            ->maxLength('bic', 255)
            ->requirePresence('bic', 'create')
            ->notEmpty('bic');

        $validator
            ->scalar('status')
            ->inList('status', ['pending', 'approved', 'rejected', 'system'])
            ->allowEmpty('status');

        return $validator;
    }

    /**
     * Validation rules for self-service registration (stricter rules)
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationRegister(Validator $validator)
    {
        // Base validation
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->scalar('name')
            ->maxLength('name', 255)
            ->requirePresence('name', 'create')
            ->notEmpty('name', 'Bitte geben Sie einen Schulnamen an.');

        // Short name is auto-generated, so just allowEmpty
        $validator
            ->scalar('kurzname')
            ->maxLength('kurzname', 20)
            ->allowEmpty('kurzname');

        // IBAN prefix is auto-generated
        $validator
            ->scalar('ibanprefix')
            ->maxLength('ibanprefix', 4)
            ->allowEmpty('ibanprefix');

        // BIC is auto-generated
        $validator
            ->scalar('bic')
            ->maxLength('bic', 255)
            ->allowEmpty('bic');

        // Status is set automatically
        $validator
            ->scalar('status')
            ->allowEmpty('status');

        return $validator;
    }

    /**
     * Sanitize input before saving (XSS protection)
     *
     * @param \Cake\Event\Event $event The event
     * @param \Cake\Datasource\EntityInterface $entity The entity being saved
     * @param \ArrayObject $options Save options
     * @return bool
     */
    public function beforeSave(Event $event, $entity, $options)
    {
        // Sanitize name
        if (isset($entity->name) && !empty($entity->name)) {
            $entity->name = strip_tags($entity->name);
            $entity->name = html_entity_decode($entity->name, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $entity->name = strip_tags($entity->name);
            $entity->name = trim($entity->name);
        }

        // Sanitize short name (important for username generation)
        if (isset($entity->kurzname) && !empty($entity->kurzname)) {
            $entity->kurzname = strip_tags($entity->kurzname);
            $entity->kurzname = html_entity_decode($entity->kurzname, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $entity->kurzname = strip_tags($entity->kurzname);
            $entity->kurzname = trim($entity->kurzname);
            // Additional: Only allow alphanumeric characters (no spaces, special chars)
            $entity->kurzname = preg_replace('/[^a-z0-9äöüß]/i', '', $entity->kurzname);
            // Convert short name to lowercase for consistency
            $entity->kurzname = strtolower($entity->kurzname);
        }

        return true;
    }
}
