<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\PublicHolidaysTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\PublicHolidaysTable Test Case
 */
class PublicHolidaysTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\PublicHolidaysTable
     */
    protected $PublicHolidays;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected array $fixtures = [
        'app.PublicHolidays',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('PublicHolidays') ? [] : ['className' => PublicHolidaysTable::class];
        $this->PublicHolidays = $this->getTableLocator()->get('PublicHolidays', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->PublicHolidays);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\PublicHolidaysTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @uses \App\Model\Table\PublicHolidaysTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
