<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Partner Entity
 * Partnerunternehmen für Überweisungen
 *
 * @property int $id
 * @property string $name
 * @property string $iban
 * @property string $bic
 * @property string $branch
 * @property string|null $description
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 */
class Partner extends Entity
{
    /**
     * Fields that can be mass assigned
     *
     * @var array
     */
    protected $_accessible = [
        'name' => true,
        'iban' => true,
        'bic' => true,
        'branch' => true,
        'description' => true,
        'created' => true,
        'modified' => true,
    ];
}
