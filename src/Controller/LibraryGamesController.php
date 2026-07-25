<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\Table\UsersTable;
use App\Service\SteamApiService;
use Cake\Core\Exception\CakeException;
use Cake\Http\Exception\NotFoundException;

/**
 * LibraryGames Controller
 *
 * @property \App\Model\Table\LibraryGamesTable $LibraryGames
 */
class LibraryGamesController extends AppController
{
    public const STATUSES = ['playing', 'completed', 'backlog', 'wishlist'];

    protected const SORT_FIELDS = ['title', 'rating', 'status'];

    protected UsersTable $Users;

    public function initialize(): void
    {
        parent::initialize();

        $this->Users = $this->fetchTable('Users');
    }

    public function index(): void
    {
        $status = $this->request->getQuery('status', '');
        $genre = trim((string)$this->request->getQuery('genre', ''));
        $sort = $this->request->getQuery('sort', 'title');
        $sort = in_array($sort, self::SORT_FIELDS, true) ? $sort : 'title';
        $direction = $this->request->getQuery('direction') === 'desc' ? 'desc' : 'asc';

        $query = $this->LibraryGames->find()
            ->where(['user_id' => $this->currentUserId()])
            ->orderBy([$sort => $direction]);

        if (in_array($status, self::STATUSES, true)) {
            $query->andWhere(['status' => $status]);
        }
        if ($genre !== '') {
            $query->andWhere(['genres LIKE' => '%' . $genre . '%']);
        }

        $libraryGames = $query->all();
        $statuses = self::STATUSES;
        $this->set(compact('libraryGames', 'statuses', 'status', 'genre', 'sort', 'direction'));
    }

    public function view(string $id): void
    {
        $libraryGame = $this->ownedLibraryGame($id);
        $user = $this->Users->get($this->currentUserId());

        $achievements = null;
        $achievementsError = null;

        if ($libraryGame->steam_appid === null) {
            $achievementsError = __('This game wasn\'t linked to Steam, so no achievement data is available.');
        } elseif ($user->steam_id64 === null) {
            $achievementsError = __('Connect your Steam account first by running an import from the "Import from Steam" page.');
        } else {
            try {
                $achievements = (new SteamApiService())->getAchievements($user->steam_id64, $libraryGame->steam_appid);
                if ($achievements === null) {
                    $achievementsError = __('No achievement data is available for this game (it may not have any, or your Steam stats are private).');
                }
            } catch (CakeException $exception) {
                $achievementsError = $exception->getMessage();
            }
        }

        $unlockedCount = $achievements !== null
            ? count(array_filter($achievements, fn (array $a): bool => $a['achieved']))
            : 0;

        $this->set(compact('libraryGame', 'achievements', 'achievementsError', 'unlockedCount'));
    }

    public function add()
    {
        $data = $this->request->getData() + ['user_id' => $this->currentUserId(), 'status' => 'backlog'];
        $libraryGame = $this->LibraryGames->newEntity($data);

        if ($this->LibraryGames->save($libraryGame)) {
            $this->Flash->success(__('{0} was added to your library.', $libraryGame->title));
        } else {
            $this->Flash->error(__('Could not add that game to your library.'));
        }

        return $this->redirect($this->referer(['action' => 'index']));
    }

    public function editStatus(string $id)
    {
        $this->request->allowMethod(['post', 'put']);

        $libraryGame = $this->ownedLibraryGame($id);
        $libraryGame = $this->LibraryGames->patchEntity($libraryGame, [
            'status' => $this->request->getData('status'),
        ]);

        if ($this->LibraryGames->save($libraryGame)) {
            $this->Flash->success(__('Status updated.'));
        } else {
            $this->Flash->error(__('Could not update the status.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function delete(string $id)
    {
        $this->request->allowMethod(['post', 'delete']);

        $libraryGame = $this->ownedLibraryGame($id);
        if ($this->LibraryGames->delete($libraryGame)) {
            $this->Flash->success(__('{0} was removed from your library.', $libraryGame->title));
        } else {
            $this->Flash->error(__('Could not remove that game.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    protected function currentUserId(): int
    {
        return (int)$this->request->getAttribute('identity')->getIdentifier();
    }

    protected function ownedLibraryGame(string $id): \App\Model\Entity\LibraryGame
    {
        $libraryGame = $this->LibraryGames->find()
            ->where(['id' => $id, 'user_id' => $this->currentUserId()])
            ->first();

        if ($libraryGame === null) {
            throw new NotFoundException();
        }

        return $libraryGame;
    }
}
