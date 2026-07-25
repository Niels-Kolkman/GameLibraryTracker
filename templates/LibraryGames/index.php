<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\LibraryGame> $libraryGames
 */
?>
<div class="library-games index content">
    <h2><?= __('My library') ?></h2>
    <p><?= $this->Html->link(__('Search for games to add'), ['controller' => 'Games', 'action' => 'search']) ?></p>

    <?php $libraryGames = iterator_to_array($libraryGames); ?>
    <?php if (!$libraryGames): ?>
        <p><?= __('Your library is empty. Search for a game above to get started.') ?></p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th><?= __('Title') ?></th>
                    <th><?= __('Genres') ?></th>
                    <th><?= __('Rating') ?></th>
                    <th><?= __('Status') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($libraryGames as $libraryGame): ?>
                    <tr>
                        <td><?= h($libraryGame->title) ?></td>
                        <td><?= h($libraryGame->genres) ?></td>
                        <td><?= h((string)$libraryGame->rating) ?></td>
                        <td><?= h($libraryGame->status) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
