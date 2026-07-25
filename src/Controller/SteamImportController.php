<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\Table\LibraryGamesTable;
use App\Service\GameTitleMatcher;
use App\Service\RawgApiService;
use App\Service\SteamApiService;
use Cake\Core\Exception\CakeException;

/**
 * SteamImport Controller
 *
 * Imports a user's public Steam library, matches each game against RAWG,
 * and lets them bulk-add the results to their in-app library.
 */
class SteamImportController extends AppController
{
    protected LibraryGamesTable $LibraryGames;

    public function initialize(): void
    {
        parent::initialize();

        $this->LibraryGames = $this->fetchTable('LibraryGames');
    }

    /**
     * Import form. On POST, resolves the Steam profile, fetches owned
     * games, matches each against RAWG, and stashes the results in the
     * session for the review step.
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        if (!$this->request->is('post')) {
            return null;
        }

        $profile = trim((string)$this->request->getData('profile'));
        if ($profile === '') {
            $this->Flash->error(__('Enter a Steam profile URL, vanity name, or SteamID64.'));

            return null;
        }

        try {
            $steamId = (new SteamApiService())->resolveSteamId($profile);
            if ($steamId === null) {
                $this->Flash->error(__('Could not find a Steam profile for "{0}".', $profile));

                return null;
            }

            $ownedGames = (new SteamApiService())->getOwnedGames($steamId);
        } catch (CakeException $exception) {
            $this->Flash->error($exception->getMessage());

            return null;
        }

        if (!$ownedGames) {
            $this->Flash->error(__('That Steam library is empty or not public.'));

            return null;
        }

        set_time_limit(120);

        $existingRawgIds = $this->LibraryGames->find()
            ->select(['rawg_id'])
            ->where(['user_id' => $this->currentUserId()])
            ->all()
            ->extract('rawg_id')
            ->toArray();

        $rawg = new RawgApiService();
        $results = [];
        foreach ($ownedGames as $game) {
            $matched = null;
            try {
                $candidates = $rawg->search($game['name'], 5);
                $matched = GameTitleMatcher::bestMatch($game['name'], $candidates);
            } catch (CakeException $exception) {
                // Leave $matched as null; the game just falls into the unmatched bucket.
            }

            $results[] = [
                'appid' => $game['appid'],
                'name' => $game['name'],
                'header_image' => $game['header_image'],
                'rawg' => $matched,
                'already_in_library' => $matched !== null && in_array($matched['rawg_id'], $existingRawgIds, true),
            ];
        }

        $this->request->getSession()->write('SteamImport.results', $results);

        return $this->redirect(['action' => 'review']);
    }

    /**
     * Shows the matched/unmatched games from the last import and lets
     * the user pick which ones to add.
     *
     * @return \Cake\Http\Response|null
     */
    public function review()
    {
        $results = $this->request->getSession()->read('SteamImport.results');
        if (!$results) {
            $this->Flash->error(__('Start a new Steam import first.'));

            return $this->redirect(['action' => 'index']);
        }

        $this->set(compact('results'));

        return null;
    }

    /**
     * Bulk-adds the selected matched games to the current user's library.
     *
     * @return \Cake\Http\Response
     */
    public function import()
    {
        $this->request->allowMethod(['post']);

        $results = $this->request->getSession()->read('SteamImport.results');
        if (!$results) {
            $this->Flash->error(__('Start a new Steam import first.'));

            return $this->redirect(['action' => 'index']);
        }

        $selectedAppIds = array_map('intval', (array)$this->request->getData('appids', []));
        $userId = $this->currentUserId();

        $added = 0;
        $skipped = 0;
        foreach ($results as $game) {
            if (!in_array($game['appid'], $selectedAppIds, true) || $game['rawg'] === null) {
                continue;
            }

            $libraryGame = $this->LibraryGames->newEntity([
                'user_id' => $userId,
                'rawg_id' => $game['rawg']['rawg_id'],
                'title' => $game['rawg']['title'],
                'cover_url' => $game['rawg']['cover_url'],
                'genres' => $game['rawg']['genres'],
                'rating' => $game['rawg']['rating'],
                'status' => 'backlog',
            ]);

            if ($this->LibraryGames->save($libraryGame)) {
                $added++;
            } else {
                $skipped++;
            }
        }

        $this->request->getSession()->delete('SteamImport.results');
        $this->Flash->success(__('Imported {0} game(s). {1} were skipped (already in your library or failed to save).', $added, $skipped));

        return $this->redirect(['controller' => 'LibraryGames', 'action' => 'index']);
    }

    protected function currentUserId(): int
    {
        return (int)$this->request->getAttribute('identity')->getIdentifier();
    }
}
