<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * LeavetypesFixture
 */
class LeavetypesFixture extends TestFixture
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
                'leave_type_id' => 1,
                'leave_type_name' => 'Lorem ipsum dolor sit amet',
            ],
        ];
        parent::init();
    }
}
