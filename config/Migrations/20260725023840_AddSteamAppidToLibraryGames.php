<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class AddSteamAppidToLibraryGames extends BaseMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/migrations/5/guides/writing-migrations/migration-methods.html#the-change-method
     *
     * @return void
     */
    public function change(): void
    {
        $table = $this->table('library_games');
        $table->addColumn('steam_appid', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => true,
        ]);
        $table->addIndex([
            'steam_appid',
        
            ], [
            'name' => 'BY_STEAM_APPID',
            'unique' => false,
        ]);
        $table->update();
    }
}
