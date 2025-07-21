<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Schedule Entity
 *
 * @property int $schedule_id
 * @property int|null $employee_id
 * @property int|null $shift_id
 * @property \Cake\I18n\Date|null $work_date
 * @property string $status
 * @property \Cake\I18n\DateTime|null $created
 *
 * @property \App\Model\Entity\Employee $employee
 * @property \App\Model\Entity\Shift $shift
 */
class Schedule extends Entity
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
        'shift_id' => true,
        'work_date' => true,
        'status' => true,
        'created' => true,
        'employee' => true,
        'shift' => true,
    ];
}
