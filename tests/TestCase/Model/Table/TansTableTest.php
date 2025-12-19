<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\TansTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\TansTable Test Case
 */
class TansTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\TansTable
     */
    public $Tans;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.tans',
        'app.accounts'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Tans') ? [] : ['className' => TansTable::class];
        $this->Tans = TableRegistry::getTableLocator()->get('Tans', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Tans);

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
