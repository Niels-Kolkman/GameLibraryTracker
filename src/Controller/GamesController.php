<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\RawgApiService;
use Cake\Core\Exception\CakeException;

/**
 * Games Controller
 *
 * Search RAWG.io for games to add to the current user's library.
 */
class GamesController extends AppController
{
    public function search(): void
    {
        $query = trim((string)$this->request->getQuery('q', ''));
        $results = [];
        $error = null;

        if ($query !== '') {
            try {
                $results = (new RawgApiService())->search($query);
            } catch (CakeException $exception) {
                $error = $exception->getMessage();
            }
        }

        $this->set(compact('query', 'results', 'error'));
    }
}
