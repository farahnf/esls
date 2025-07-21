<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * ShiftsFixture
 */
class ShiftsFixture extends TestFixture
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
                'shift_id' => 1,
                'shift_name' => 'Lorem ipsum dolor sit amet',
                'start_time' => '07:32:43',
                'end_time' => '07:32:43',
            ],
        ];
        parent::init();
    }
}
