<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Employee Entity
 *
 * @property int $employee_id
 * @property string|null $full_name
 * @property string $email
 * @property string $phone
 * @property \Cake\I18n\Date $hire_date
 */
class Employee extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array<string, bool>
     */
    protected array $_accessible = [
        'full_name' => true,
    'email' => true,
    'phone' => true,
    'hire_date' => true,
    'rest_day' => true, // ✅ Must be included
    ];
}
