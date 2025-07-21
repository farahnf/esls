<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\LeavetypesTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\LeavetypesTable Test Case
 */
class LeavetypesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\LeavetypesTable
     */
    protected $Leavetypes;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected array $fixtures = [
        'app.Leavetypes',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Leavetypes') ? [] : ['className' => LeavetypesTable::class];
        $this->Leavetypes = $this->getTableLocator()->get('Leavetypes', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->Leavetypes);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\LeavetypesTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
