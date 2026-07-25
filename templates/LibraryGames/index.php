<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\LibraryGame> $libraryGames
 * @var array<string> $statuses
 * @var string $status
 * @var string $genre
 * @var string $sort
 * @var string $direction
 */

$sortLink = function (string $field, string $label) use ($sort, $direction, $status, $genre) {
    $nextDirection = ($sort === $field && $direction === 'asc') ? 'desc' : 'asc';
    $active = $sort === $field ? ' (' . $direction . ')' : '';

    return $this->Html->link($label . $active, [
        '?' => compact('status', 'genre') + ['sort' => $field, 'direction' => $nextDirection],
    ]);
};
?>
<div class="library-games index content">
    <h2><?= __('My library') ?></h2>
    <p><?= $this->Html->link(__('Search for games to add'), ['controller' => 'Games', 'action' => 'search']) ?></p>

    <?= $this->Form->create(null, ['type' => 'get']) ?>
    <?= $this->Form->control('status', [
        'options' => ['' => __('All statuses')] + array_combine($statuses, $statuses),
        'value' => $status,
        'label' => __('Status'),
    ]) ?>
    <?= $this->Form->control('genre', ['value' => $genre, 'label' => __('Genre contains'), 'placeholder' => __('e.g. RPG')]) ?>
    <?= $this->Form->button(__('Filter')) ?>
    <?= $this->Html->link(__('Clear filters'), ['action' => 'index']) ?>
    <?= $this->Form->end() ?>

    <?php $libraryGames = iterator_to_array($libraryGames); ?>
    <?php if (!$libraryGames): ?>
        <p><?= __('No games match your library yet.') ?></p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th><?= $sortLink('title', __('Title')) ?></th>
                    <th><?= __('Genres') ?></th>
                    <th><?= $sortLink('rating', __('Rating')) ?></th>
                    <th><?= $sortLink('status', __('Status')) ?></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($libraryGames as $libraryGame): ?>
                    <tr>
                        <td><?= h($libraryGame->title) ?></td>
                        <td><?= h($libraryGame->genres) ?></td>
                        <td><?= h((string)$libraryGame->rating) ?></td>
                        <td>
                            <?= $this->Form->create(null, ['url' => ['action' => 'editStatus', $libraryGame->id]]) ?>
                            <?= $this->Form->select('status', array_combine($statuses, $statuses), [
                                'value' => $libraryGame->status,
                                'onchange' => 'this.form.submit()',
                            ]) ?>
                            <?= $this->Form->end() ?>
                        </td>
                        <td>
                            <?= $this->Form->postLink(
                                __('Remove'),
                                ['action' => 'delete', $libraryGame->id],
                                ['confirm' => __('Remove {0} from your library?', $libraryGame->title)],
                            ) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
