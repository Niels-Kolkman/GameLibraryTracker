<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * LibraryGames Controller
 *
 * @property \App\Model\Table\LibraryGamesTable $LibraryGames
 */
class LibraryGamesController extends AppController
{
    public function index(): void
    {
        $userId = $this->request->getAttribute('identity')->getIdentifier();

        $libraryGames = $this->LibraryGames->find()
            ->where(['user_id' => $userId])
            ->orderBy(['title' => 'ASC'])
            ->all();

        $this->set(compact('libraryGames'));
    }

    public function add()
    {
        $userId = $this->request->getAttribute('identity')->getIdentifier();

        $data = $this->request->getData() + ['user_id' => $userId, 'status' => 'backlog'];
        $libraryGame = $this->LibraryGames->newEntity($data);

        if ($this->LibraryGames->save($libraryGame)) {
            $this->Flash->success(__('{0} was added to your library.', $libraryGame->title));
        } else {
            $this->Flash->error(__('Could not add that game to your library.'));
        }

        return $this->redirect($this->referer(['action' => 'index']));
    }
}
