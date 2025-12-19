<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Transaction Entity
 *
 * @property int $id
 * @property int $account_id
 * @property string $empfaenger_name
 * @property string $empfaenger_adresse
 * @property string $empfaenger_iban
 * @property string $empfaenger_bic
 * @property float $betrag
 * @property \Cake\I18n\FrozenDate $datum
 * @property string $zahlungszweck
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\Account $account
 */
class Transaction extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        'account_id' => true,
        'empfaenger_name' => true,
        'empfaenger_adresse' => true,
        'empfaenger_iban' => true,
        'empfaenger_bic' => true,
        'betrag' => true,
        'datum' => true,
        'zahlungszweck' => true,
        'created' => true,
        'modified' => true,
        'account' => true
    ];
}
