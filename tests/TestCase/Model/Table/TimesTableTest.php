<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\TimesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\TimesTable Test Case
 */
class TimesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\TimesTable
     */
    public $Times;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Times',
        'app.Users',
        'app.Projects',
        'app.Tasks'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Times') ? [] : ['className' => TimesTable::class];
        $this->Times = TableRegistry::getTableLocator()->get('Times', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Times);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     */
    public function testBuildRules()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
