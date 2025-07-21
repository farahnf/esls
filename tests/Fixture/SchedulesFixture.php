<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * SchedulesFixture
 */
class SchedulesFixture extends TestFixture
{
    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            [
                'schedule_id' => 1,
                'employee_id' => 1,
                'shift_id' => 1,
                'work_date' => '2025-07-01',
                'status' => 'Lorem ipsum dolor sit amet',
                'created' => '2025-07-01 07:46:57',
            ],
        ];
        parent::init();
    }
}
