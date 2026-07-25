<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * LibraryGamesFixture
 */
class LibraryGamesFixture extends TestFixture
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
                'id' => 1,
                'user_id' => 1,
                'rawg_id' => 1,
                'title' => 'Lorem ipsum dolor sit amet',
                'cover_url' => 'Lorem ipsum dolor sit amet',
                'genres' => 'Lorem ipsum dolor sit amet',
                'rating' => 1.5,
                'status' => 'Lorem ipsum dolor ',
                'created' => '2026-07-25 01:35:02',
                'modified' => '2026-07-25 01:35:02',
            ],
        ];
        parent::init();
    }
}
