<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Leave Entity
 *
 * @property int $leave_id
 * @property int|null $employee_id
 * @property int|null $leave_type_id
 * @property \Cake\I18n\Date|null $start_date
 * @property \Cake\I18n\Date|null $end_date
 * @property string|null $reason
 * @property string|null $status
 * @property \Cake\I18n\DateTime|null $applied_on
 * @property \Cake\I18n\DateTime|null $created
 *
 * @property \App\Model\Entity\Employee $employee
 * @property \App\Model\Entity\Leavetype $leave_type
 */
class Leave extends Entity
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
    'employee_id' => true,
    'leave_type_id' => true,
    'start_date' => true,
    'end_date' => true,
    'reason' => true,
    'status' => true,
    'applied_on' => true,
    'created' => true,
    'leave_category_id' => true,
    'total_days' => true,
    'deduct_from_annual' => true,
    'is_unpaid' => true,
       '*' => true,
];
}
