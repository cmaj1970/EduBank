<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * School Entity
 *
 * @property int $id
 * @property string $name
 * @property string $kurzname
 * @property string|null $contact_person
 * @property string|null $contact_email
 * @property string $ibanprefix
 * @property string $bic
 * @property string $status
 * @property string|null $verification_token
 * @property \Cake\I18n\FrozenTime|null $verified_at
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\User[] $users
 */
class School extends Entity
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
        'name' => true,
        'kurzname' => true,
        'ibanprefix' => true,
        'bic' => true,
        'status' => true,
        'verification_token' => true,
        'verified_at' => true,
        'contact_person' => true,
        'contact_email' => true,
        'created' => true,
        'modified' => true,
        'users' => true
    ];
}
