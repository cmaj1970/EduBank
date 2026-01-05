<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Event\Event;

/**
 * Transactions Model
 *
 * @property \App\Model\Table\AccountsTable|\Cake\ORM\Association\BelongsTo $Accounts
 *
 * @method \App\Model\Entity\Transaction get($primaryKey, $options = [])
 * @method \App\Model\Entity\Transaction newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Transaction[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Transaction|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Transaction|bool saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Transaction patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Transaction[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Transaction findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class TransactionsTable extends Table
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

        $this->setTable('transactions');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Accounts', [
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
            ->scalar('empfaenger_name')
            ->maxLength('empfaenger_name', 255)
            ->notEmpty('empfaenger_name', 'Bitte einen Empfängernamen eingeben');

        $validator
            ->scalar('empfaenger_adresse')
            ->maxLength('empfaenger_adresse', 255)
            ->allowEmpty('empfaenger_adresse');

        $validator
            ->scalar('empfaenger_iban')
            ->maxLength('empfaenger_iban', 255)
            ->notEmpty('empfaenger_iban', 'Bitte eine IBAN eingeben');

        $validator
            ->scalar('empfaenger_bic')
            ->maxLength('empfaenger_bic', 255)
            ->allowEmpty('empfaenger_bic');

        $validator
            ->decimal('betrag')
            ->notEmpty('betrag', 'Bitte einen Betrag eingeben')
            ;

        $validator
            ->date('datum')
            ->allowEmpty('datum');

        $validator
            ->scalar('zahlungszweck')
            ->notEmpty('zahlungszweck', 'Bitte Zahlungszweck/Zahlungsrefernz eingeben');

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
        $rules->add($rules->existsIn(['account_id'], 'Accounts'));

        return $rules;
    }

    /**
     * Sanitize input before saving (XSS protection)
     * Removes all HTML/JavaScript tags from free text fields
     *
     * @param \Cake\Event\Event $event The event
     * @param \Cake\Datasource\EntityInterface $entity The entity being saved
     * @param \ArrayObject $options Save options
     * @return bool
     */
    public function beforeSave(Event $event, $entity, $options)
    {
        // List of fields that could contain HTML tags
        $fieldsToSanitize = ['empfaenger_name', 'empfaenger_adresse', 'zahlungszweck'];

        foreach ($fieldsToSanitize as $field) {
            if (isset($entity->$field) && !empty($entity->$field)) {
                // Remove all HTML/JavaScript tags, keep only text
                $entity->$field = strip_tags($entity->$field);

                // Additional: Decode entities (prevents &lt;script&gt; tricks)
                $entity->$field = html_entity_decode($entity->$field, ENT_QUOTES | ENT_HTML5, 'UTF-8');

                // Strip tags again (in case entity_decode created new tags)
                $entity->$field = strip_tags($entity->$field);

                // Normalize whitespace
                $entity->$field = trim($entity->$field);
            }
        }

        return true;
    }
}
