<?php

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Partners Model
 * Partnerunternehmen für Überweisungen (fiktive Geschäftspartner)
 */
class PartnersTable extends Table
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

        $this->setTable('partners');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
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
            ->scalar('iban')
            ->maxLength('iban', 20)
            ->requirePresence('iban', 'create')
            ->notEmpty('iban')
            ->add('iban', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->scalar('bic')
            ->maxLength('bic', 11)
            ->notEmpty('bic');

        $validator
            ->scalar('branch')
            ->maxLength('branch', 100)
            ->requirePresence('branch', 'create')
            ->notEmpty('branch');

        $validator
            ->scalar('description')
            ->maxLength('description', 255)
            ->allowEmpty('description');

        return $validator;
    }

    /**
     * Alle Partner nach Branchen gruppiert
     *
     * @return array
     */
    public function getGroupedByBranch()
    {
        $partners = $this->find()
            ->order(['branch' => 'ASC', 'name' => 'ASC'])
            ->toArray();

        $grouped = [];
        foreach ($partners as $partner) {
            if (!isset($grouped[$partner->branch])) {
                $grouped[$partner->branch] = [];
            }
            $grouped[$partner->branch][] = $partner;
        }

        return $grouped;
    }

    /**
     * Partner anhand IBAN finden
     *
     * @param string $iban Die IBAN
     * @return \App\Model\Entity\Partner|null
     */
    public function findByIban($iban)
    {
        return $this->find()
            ->where(['iban' => $iban])
            ->first();
    }
}
