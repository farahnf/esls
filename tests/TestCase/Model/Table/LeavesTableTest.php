<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\LeavesTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\LeavesTable Test Case
 */
class LeavesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\LeavesTable
     */
    protected $Leaves;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected array $fixtures = [
        'app.Leaves',
        'app.Employees',
        'app.LeaveTypes',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Leaves') ? [] : ['className' => LeavesTable::class];
        $this->Leaves = $this->getTableLocator()->get('Leaves', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->Leaves);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\LeavesTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @uses \App\Model\Table\LeavesTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
