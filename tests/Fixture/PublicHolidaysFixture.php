<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * PublicHolidaysFixture
 */
class PublicHolidaysFixture extends TestFixture
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
                'holiday_id' => 1,
                'holiday_date' => '2025-07-17',
                'description' => 'Lorem ipsum dolor sit amet',
                'created' => '2025-07-17 02:20:33',
                'modified' => '2025-07-17 02:20:33',
            ],
        ];
        parent::init();
    }
}
