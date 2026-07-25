<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\LibraryGamesTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\LibraryGamesTable Test Case
 */
class LibraryGamesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\LibraryGamesTable
     */
    protected $LibraryGames;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected array $fixtures = [
        'app.LibraryGames',
        'app.Users',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('LibraryGames') ? [] : ['className' => LibraryGamesTable::class];
        $this->LibraryGames = $this->getTableLocator()->get('LibraryGames', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->LibraryGames);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @link \App\Model\Table\LibraryGamesTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @link \App\Model\Table\LibraryGamesTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
